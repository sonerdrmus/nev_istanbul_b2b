<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\BannerSlide;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariationOption;
use App\Models\ShippingMethod;
use App\Models\SizeTable;
use App\Support\ProductVariationFlowSteps;
use App\Support\MediaUrl;
use App\Support\DimensionMultiplierCatalog;
use App\Support\PackagingPreferenceCatalog;
use App\Support\ProductCustomizationCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class StoreController extends Controller
{
    private function cartKey(int $productId): string
    {
        return (string) $productId;
    }

    private function getCart(): array
    {
        return session('cart', []);
    }

    /**
     * Aynı beden etiketi birden fazla tabloda varsa, tablo sort_order + kolon sırasına göre ilk eşleşen çarpan kullanılır.
     *
     * @return array<string, float> size_value => multiplier
     */
    private function buildSizeValueMultiplierMap(): array
    {
        if (! Schema::hasColumn('size_table_columns', 'price_multiplier')) {
            return [];
        }
        $map = [];
        $tables = SizeTable::with(['columns' => fn ($q) => $q->orderBy('sort_order')->orderBy('id')])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
        foreach ($tables as $table) {
            foreach ($table->columns as $col) {
                if (! array_key_exists($col->size_value, $map)) {
                    $m = (float) ($col->price_multiplier ?? 1);
                    $map[$col->size_value] = max(0.0, $m);
                }
            }
        }

        return $map;
    }

    /** Beden adetleriyle fiyat ağırlığı: Σ(adet × çarpan). */
    private function sizePricingWeightFromQuantities(array $sizeQuantities, array $multiplierMap): float
    {
        $w = 0.0;
        foreach ($sizeQuantities as $label => $qty) {
            $q = (int) $qty;
            if ($q <= 0) {
                continue;
            }
            $m = $multiplierMap[(string) $label] ?? 1.0;
            $w += $q * $m;
        }

        return $w;
    }

    /** @return \Illuminate\Support\Collection<int, object{product: Product, quantity: int, subtotal: float, cart_key: string, unit_price_try: float}> */
    private function getCartItems(): \Illuminate\Support\Collection
    {
        $cart = $this->getCart();
        if (empty($cart)) {
            return collect();
        }
        $productIds = array_unique(array_column($cart, 'product_id'));
        $products = Product::with(['company', 'currency'])->whereIn('id', $productIds)->get()->keyBy('id');
        $items = collect();
        $sizeMultiplierMap = $this->buildSizeValueMultiplierMap();
        foreach ($cart as $key => $item) {
            $product = $products->get($item['product_id'] ?? 0);
            if (! $product || ($item['quantity'] ?? 0) < 1) {
                continue;
            }
            $qty = (int) $item['quantity'];
            $productCurrency = $product->currency ?? Currency::getDefault();
            $customerGroupId = auth()->check() && auth()->user()->company?->customer_group_id
                ? (int) auth()->user()->company->customer_group_id
                : 1;

            $discountUnitTry = $product->getDiscountUnitPriceInTRY($qty, $customerGroupId);
            $unitTry = $discountUnitTry !== null
                ? $discountUnitTry
                : ($productCurrency && $productCurrency->code !== 'TRY'
                    ? $productCurrency->convertToTRY((float) $product->price)
                    : (float) $product->price);

            $discountPercent = $this->getCustomerDiscountPercent();
            if ($discountPercent !== null && $discountPercent > 0) {
                $unitTry = $unitTry * (1 - $discountPercent / 100);
            }

            $variationData = $item['variation_data'] ?? null;
            if (is_array($variationData) && $variationData !== []) {
                $product->loadMissing('variations.options');
                $unitTry *= ProductVariationOption::combinedMultiplierForSelections($product, $variationData);
                $unitTry += ProductVariationOption::additiveExtraTryForSelections($variationData);
            }

            $sizeQuantities = $item['size_quantities'] ?? null;
            $pricingWeight = $qty;
            if (is_array($sizeQuantities) && $sizeQuantities !== [] && $sizeMultiplierMap !== []) {
                $pricingWeight = $this->sizePricingWeightFromQuantities($sizeQuantities, $sizeMultiplierMap);
            } elseif (is_array($sizeQuantities) && $sizeQuantities !== [] && $sizeMultiplierMap === []) {
                $pricingWeight = (float) array_sum(array_map('intval', $sizeQuantities));
            }

            $items->push((object) [
                'product' => $product,
                'quantity' => $qty,
                'subtotal' => $unitTry * $pricingWeight,
                'cart_key' => $key,
                'unit_price_try' => $unitTry,
                'variation_data' => $item['variation_data'] ?? null,
                'size_quantities' => $item['size_quantities'] ?? null,
            ]);
        }

        return $items;
    }

    public function index(): View
    {
        $baseQuery = Product::with(['company', 'category.parent', 'currency', 'productImages'])
            ->active()
            ->visibleInStore();

        $parentSlug = request('parent');
        $categorySlug = request('category');
        $currentCategory = null;
        if ($categorySlug) {
            $currentCategory = Category::with(['parent', 'children'])->where('slug', $categorySlug)->first();
            if ($currentCategory) {
                if ($currentCategory->children->isNotEmpty()) {
                    $childIds = $currentCategory->children->pluck('id')->all();
                    $baseQuery->whereIn('category_id', $childIds);
                } else {
                    $baseQuery->where('category_id', $currentCategory->id);
                }
            }
        } elseif ($parentSlug) {
            $currentCategory = Category::with(['children'])->where('slug', $parentSlug)->first();
            if ($currentCategory && $currentCategory->children->isNotEmpty()) {
                $childIds = $currentCategory->children->pluck('id')->all();
                $baseQuery->whereIn('category_id', $childIds);
            }
        }

        $searchQuery = request('q');
        if ($searchQuery !== null && $searchQuery !== '') {
            $baseQuery->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', '%' . $searchQuery . '%')
                    ->orWhere('description', 'like', '%' . $searchQuery . '%');
            });
        }

        // Filtreler: marka, stok, durum
        $companyId = request('company');
        if ($companyId !== null && $companyId !== '') {
            $baseQuery->where('company_id', (int) $companyId);
        }
        if (request('in_stock') === '1') {
            $baseQuery->where(function ($q) {
                $q->whereNull('stock_quantity')->orWhere('stock_quantity', '>', 0);
            });
        }
        $statusSatista = request('status_satista');
        $statusYakinda = request('status_yakinda');
        if ($statusSatista === '1' && $statusYakinda !== '1') {
            $baseQuery->where('status', 'satista');
        } elseif ($statusYakinda === '1' && $statusSatista !== '1') {
            $baseQuery->where('status', 'yakinda_gelecek');
        } elseif ($statusSatista === '1' && $statusYakinda === '1') {
            // İkisi de seçili: zaten visibleInStore = satista + yakinda
        }

        // Sıralama
        $sort = request('sort', 'default');
        match ($sort) {
            'name_asc' => $baseQuery->orderBy('name'),
            'name_desc' => $baseQuery->orderByDesc('name'),
            'price_asc' => $baseQuery->orderBy('price'),
            'price_desc' => $baseQuery->orderByDesc('price'),
            'newest' => $baseQuery->orderByDesc('created_at'),
            default => $baseQuery->orderBy('sort_order')->orderBy('name'),
        };

        $perPage = (int) request('per_page', 12);
        $perPage = in_array($perPage, [12, 20, 40, 60], true) ? $perPage : 12;
        $products = $baseQuery->paginate($perPage)->withQueryString();

        $cartCount = collect($this->getCart())->sum('quantity');
        $canSeePrices = auth()->check();
        $customerDiscountPercent = $this->getCustomerDiscountPercent();

        $currencies = $this->getCurrenciesForCurrentUser();
        $selectedCurrencyCode = request('currency', session('store_currency', 'TRY'));
        $selectedCurrency = $currencies->firstWhere('code', $selectedCurrencyCode) ?? $currencies->first() ?? Currency::getDefault();
        if ($selectedCurrency) {
            session(['store_currency' => $selectedCurrency->code]);
        }

        $bannerSlides = collect();
        $homeCategoryShowcase = collect();
        $homeProductShowcase = collect();
        if (! $currentCategory) {
            $bannerSlides = BannerSlide::forHome()->get();
            if (Schema::hasColumn('categories', 'image_path')) {
                $homeCategoryShowcase = Category::query()
                    ->active()
                    ->whereNotNull('image_path')
                    ->where('image_path', '!=', '')
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get();
            }
            if (Schema::hasColumn('products', 'show_on_home')) {
                $homeProductShowcase = Product::query()
                    ->active()
                    ->visibleInStore()
                    ->where('show_on_home', true)
                    ->with(['productImages' => fn ($q) => $q->orderBy('sort_order')->orderBy('id')])
                    ->orderBy('home_showcase_order')
                    ->orderBy('name')
                    ->get();
            }
        }

        return view('store.index', compact('products', 'cartCount', 'canSeePrices', 'currentCategory', 'selectedCurrency', 'currencies', 'searchQuery', 'customerDiscountPercent', 'bannerSlides', 'homeCategoryShowcase', 'homeProductShowcase'));
    }

    /** Giriş yapmış müşterinin şirketindeki kâr marjı (indirim) yüzdesi; yoksa null */
    private function getCustomerDiscountPercent(): ?float
    {
        if (! auth()->check()) {
            return null;
        }
        $company = auth()->user()->company;
        if (! $company) {
            return null;
        }
        $pct = (float) $company->profit_margin_percentage;

        return $pct > 0 ? $pct : null;
    }

    /** Giriş yapmış bayi için izin verilen para birimleri; misafir veya admin için tüm aktif para birimleri. */
    private function getCurrenciesForCurrentUser(): Collection
    {
        return Currency::forCurrentUserWithTcmbSpot();
    }

    /** Ürün detay sayfası — misafirler de görebilir */
    public function showProduct(Product $product): View
    {
        if (! $product->is_active) {
            abort(404);
        }
        $product->load([
            'company',
            'category.parent',
            'currency',
            'productImages',
            'variations.options' => fn ($q) => $q->with([
                'interfaceColorVariation.fabricTypeVariation',
                'interfaceFabricTypeVariation',
                'interfaceLabelTypeVariation',
                'interfacePackagingPreferenceVariation',
                'interfaceCertificateVariation',
                'interfaceDeliveryMethodVariation',
                'sizeTable.columns',
            ]),
        ]);
        $product->setRelation('variations', ProductVariationFlowSteps::topologicallySorted($product->variations));
        $canSeePrices = auth()->check();
        $customerDiscountPercent = $this->getCustomerDiscountPercent();
        $customerGroupId = auth()->check() && auth()->user()->company?->customer_group_id
            ? (int) auth()->user()->company->customer_group_id
            : 1;
        $currencies = $this->getCurrenciesForCurrentUser();
        $selectedCurrencyCode = request('currency', session('store_currency', 'TRY'));
        $selectedCurrency = $currencies->firstWhere('code', $selectedCurrencyCode) ?? $currencies->first() ?? Currency::getDefault();
        if ($selectedCurrency) {
            session(['store_currency' => $selectedCurrency->code]);
        }

        $sizeTables = SizeTable::with('columns')->orderBy('sort_order')->get();
        $productCustomization = ($product->customization_enabled ?? true)
            ? ProductCustomizationCatalog::forStore()
            : ['rows' => [], 'print_techniques' => [], 'default_print_slug' => 'emprime', 'max_color_count' => 7];
        $dimensionMultipliersByPrint = DimensionMultiplierCatalog::groupedForStore();
        $packagingCatalog = PackagingPreferenceCatalog::forStore();

        return view('store.product', compact('product', 'canSeePrices', 'selectedCurrency', 'currencies', 'customerDiscountPercent', 'customerGroupId', 'sizeTables', 'productCustomization', 'dimensionMultipliersByPrint', 'packagingCatalog'));
    }

    /** Header arama için: 3+ karakterde ürün listesi (görsel + isim) JSON döner. */
    public function searchProducts(Request $request): \Illuminate\Http\JsonResponse
    {
        $q = trim((string) $request->get('q', ''));
        if (strlen($q) < 3) {
            return response()->json(['products' => []]);
        }
        $products = Product::with('category')
            ->active()
            ->visibleInStore()
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', '%' . $q . '%')
                    ->orWhere('description', 'like', '%' . $q . '%');
            })
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'slug', 'image']);
        $items = $products->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'url' => route('store.product.show', $p),
                'image' => $p->image ? MediaUrl::public($p->image) : null,
            ];
        });
        return response()->json(['products' => $items->all()]);
    }

    public function cart(): View
    {
        $cartItems = $this->getCartItems();
        $cartTotal = $cartItems->sum('subtotal');
        $cartCount = $cartItems->sum('quantity');

        $currencies = $this->getCurrenciesForCurrentUser();
        $selectedCurrencyCode = request('currency', session('store_currency', 'TRY'));
        $selectedCurrency = $currencies->firstWhere('code', $selectedCurrencyCode) ?? $currencies->first() ?? Currency::getDefault();
        if ($selectedCurrency) {
            session(['store_currency' => $selectedCurrency->code]);
        }

        return view('store.cart', compact('cartItems', 'cartTotal', 'cartCount', 'selectedCurrency', 'currencies'));
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
            'variation_data' => 'nullable|string',
            'size_quantities' => 'nullable|string',
        ]);
        $product = Product::findOrFail($request->product_id);
        if (! $product->isOnSale()) {
            return redirect()->back()->with('error', __('store.flash.product_not_for_sale'));
        }
        $availableStock = $product->getAvailableStock();
        if ($availableStock < 1) {
            return redirect()->back()->with('error', __('store.flash.out_of_stock'));
        }
        $minOrder = $product->getMinimumOrderQuantity();
        $sizeQuantities = null;
        if (! empty($request->size_quantities)) {
            $decoded = json_decode($request->size_quantities, true);
            if (is_array($decoded)) {
                $sizeQuantities = array_map('intval', $decoded);
                $qtyFromSizes = array_sum($sizeQuantities);
                $qty = $qtyFromSizes;
                if ($qty < $minOrder) {
                    return redirect()->back()->with('error', __('store.flash.min_qty_sizes', ['min' => $minOrder, 'qty' => $qty]));
                }
            }
        }
        if ($sizeQuantities === null) {
            $qty = (int) ($request->quantity ?: $minOrder);
        }
        if ($qty < $minOrder) {
            return redirect()->back()->with('error', __('store.flash.min_qty', ['min' => $minOrder]));
        }
        if ($qty > $availableStock) {
            return redirect()->back()->with('error', __('store.flash.max_stock', ['max' => $availableStock]));
        }
        $variationData = null;
        if (! empty($request->variation_data)) {
            $decoded = json_decode($request->variation_data, true);
            if (is_array($decoded) && ! empty($decoded)) {
                $variationData = $decoded;
            }
        }

        // Varyasyonu olan ürünlerde tüm (kök) varyasyonların seçilmesi zorunlu
        $product->load('variations');
        $rootVariations = $product->variations->filter(fn ($v) => empty($v->depends_on))->pluck('name')->unique()->values();
        if ($rootVariations->isNotEmpty()) {
            if (empty($variationData) || ! is_array($variationData)) {
                return redirect()->back()->with('error', __('store.flash.select_all_options'));
            }
            foreach ($rootVariations as $variationName) {
                if (! isset($variationData[$variationName])) {
                    return redirect()->back()->with('error', __('store.flash.select_option_named', ['name' => $variationName]));
                }
                $val = $variationData[$variationName];
                if (is_array($val) && array_key_exists('option', $val)) {
                    if (trim((string) ($val['option'] ?? '')) === '') {
                        return redirect()->back()->with('error', __('store.flash.select_option_named', ['name' => $variationName]));
                    }

                    continue;
                }
                if (is_array($val)) {
                    $nonEmpty = array_values(array_filter($val, fn ($x) => $x !== null && trim((string) $x) !== ''));
                    if ($nonEmpty === []) {
                        return redirect()->back()->with('error', __('store.flash.select_option_named', ['name' => $variationName]));
                    }
                } elseif ((string) $val === '') {
                    return redirect()->back()->with('error', __('store.flash.select_option_named', ['name' => $variationName]));
                }
            }
        }

        $key = $this->cartKey((int) $product->id);
        $cart = $this->getCart();
        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $qty;
        } else {
            $cart[$key] = [
                'product_id' => $product->id,
                'quantity' => $qty,
            ];
        }
        if ($variationData !== null) {
            $cart[$key]['variation_data'] = $variationData;
        }
        if ($sizeQuantities !== null) {
            $cart[$key]['size_quantities'] = $sizeQuantities;
        }
        session(['cart' => $cart]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'cart_count' => collect($cart)->sum('quantity')]);
        }

        return redirect()->route('store.cart')->with('success', __('store.flash.cart_added'));
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.cart_key' => 'required|string',
            'items.*.quantity' => 'required|integer|min:0',
        ]);
        $cart = $this->getCart();
        $productIds = array_unique(array_column($cart, 'product_id'));
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        foreach ($request->items as $item) {
            $key = $item['cart_key'];
            $qty = (int) $item['quantity'];
            if (isset($cart[$key])) {
                $product = $products->get($cart[$key]['product_id'] ?? 0);
                $minOrder = $product ? $product->getMinimumOrderQuantity() : 1;
                if ($qty > 0) {
                    if ($qty < $minOrder) {
                        return redirect()->route('store.cart')->with('error', __('store.flash.min_qty_named', [
                            'name' => $product?->name ?? __('store.generic.product'),
                            'min' => $minOrder,
                        ]));
                    }
                    $cart[$key]['quantity'] = $qty;
                } else {
                    unset($cart[$key]);
                }
            }
        }
        session(['cart' => $cart]);

        return redirect()->route('store.cart')->with('success', __('store.flash.cart_updated'));
    }

    public function removeFromCart(Request $request, string $cartKey)
    {
        $cart = $this->getCart();
        unset($cart[$cartKey]);
        session(['cart' => $cart]);

        return redirect()->route('store.cart')->with('success', __('store.flash.cart_removed'));
    }

    public function checkout(): View|\Illuminate\Http\RedirectResponse
    {
        $cartItems = $this->getCartItems();
        if ($cartItems->isEmpty()) {
            return redirect()->route('home')->with('info', __('store.flash.cart_empty'));
        }
        $cartTotal = $cartItems->sum('subtotal');
        $cartCount = $cartItems->sum('quantity');

        $currencies = $this->getCurrenciesForCurrentUser();
        $selectedCurrencyCode = request('currency', session('store_currency', 'TRY'));
        $selectedCurrency = $currencies->firstWhere('code', $selectedCurrencyCode) ?? $currencies->first() ?? Currency::getDefault();
        if ($selectedCurrency) {
            session(['store_currency' => $selectedCurrency->code]);
        }

        $shippingMethods = ShippingMethod::active()->orderBy('sort_order')->get();
        $bankAccounts = BankAccount::active()->orderBy('sort_order')->get();

        return view('store.checkout', compact('cartItems', 'cartTotal', 'cartCount', 'selectedCurrency', 'currencies', 'shippingMethods', 'bankAccounts'));
    }

    public function placeOrder(Request $request)
    {
        $cartItems = $this->getCartItems();
        if ($cartItems->isEmpty()) {
            return redirect()->route('home')->with('error', __('store.flash.cart_empty_checkout'));
        }

        $shippingMethods = ShippingMethod::active()->orderBy('sort_order')->get();
        $bankAccounts = BankAccount::active()->orderBy('sort_order')->get();
        $cartTotal = $cartItems->sum('subtotal');
        $rules = [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'nullable|string|max:50',
            'customer_address' => 'nullable|string|max:500',
            'payment_method' => 'required|in:havale',
            'notes' => 'nullable|string|max:1000',
        ];
        if ($shippingMethods->isNotEmpty()) {
            $rules['shipping_method_id'] = 'required|exists:shipping_methods,id';
        }
        if ($bankAccounts->isNotEmpty()) {
            $rules['bank_account_id'] = 'required|exists:bank_accounts,id';
        }
        $request->validate($rules);

        $shippingMethod = null;
        $shippingCost = 0.0;
        if ($shippingMethods->isNotEmpty()) {
            $shippingMethod = ShippingMethod::find($request->shipping_method_id);
            if ($shippingMethod && $shippingMethod->is_active) {
                $shippingCost = $shippingMethod->getCostForCartTotal($cartTotal);
            }
        }
        $total = $cartTotal + $shippingCost;

        DB::beginTransaction();
        try {
            $bankAccountId = null;
            if ($bankAccounts->isNotEmpty()) {
                $selectedBank = BankAccount::where('id', $request->bank_account_id)->where('is_active', true)->first();
                $bankAccountId = $selectedBank?->id;
            }

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'payment_method' => 'havale',
                'bank_account_id' => $bankAccountId,
                'status' => 'pending',
                'total' => $total,
                'shipping_method_id' => $shippingMethod?->id,
                'shipping_cost' => $shippingCost,
                'notes' => $request->notes,
            ]);

            foreach ($cartItems as $item) {
                $unitTry = (float) $item->unit_price_try;
                $variationData = $item->variation_data ?? [];
                if (! empty($item->size_quantities)) {
                    $variationData['size_quantities'] = $item->size_quantities;
                }
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product->id,
                    'product_name' => (string) $item->product->name,
                    'price' => round($unitTry, 2),
                    'quantity' => (int) $item->quantity,
                    'subtotal' => round((float) $item->subtotal, 2),
                    'variation_data' => ! empty($variationData) ? $variationData : null,
                ]);
            }

            DB::commit();
            session()->forget('cart');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Sipariş oluşturma hatası', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            $errorMessage = __('store.flash.order_error');
            if (config('app.debug')) {
                $errorMessage .= ' ' . $e->getMessage();
            }

            return redirect()->route('store.checkout')->with('error', $errorMessage)->withInput();
        }

        return redirect()->route('store.order-confirmation', $order)->with('success', __('store.flash.order_placed'));
    }

    public function orderConfirmation(Order $order): View
    {
        $order->load(['items', 'shippingMethod', 'bankAccount']);

        $currencies = $this->getCurrenciesForCurrentUser();
        $selectedCurrencyCode = request('currency', session('store_currency', 'TRY'));
        $selectedCurrency = $currencies->firstWhere('code', $selectedCurrencyCode) ?? $currencies->first() ?? Currency::getDefault();
        if ($selectedCurrency) {
            session(['store_currency' => $selectedCurrency->code]);
        }

        return view('store.order-confirmation', compact('order', 'selectedCurrency', 'currencies'));
    }

    /** Mağaza header’daki giriş modalından POST; panel (müşteri) girişi. */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::guard('web')->attempt($request->only('email', 'password'), (bool) $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'))->with('success', __('store.flash.login_ok'));
        }

        return back()->withErrors(['email' => __('store.login_failed')])->withInput($request->only('email'))->with('open_login_modal', true);
    }

    /** Bayi başvuru sayfası: Neden Bayi + form. */
    public function dealerRegistrationPage(): View
    {
        return view('store.bayi-ol');
    }
}
