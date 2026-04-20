<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\BannerSlide;
use App\Models\Category;
use App\Models\Currency;
use App\Models\HomeSection;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\SizeTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

            $items->push((object) [
                'product' => $product,
                'quantity' => $qty,
                'subtotal' => $unitTry * $qty,
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
        $homeSections = collect();
        if (! $currentCategory) {
            $bannerSlides = BannerSlide::forHome()->get();
            $homeSections = HomeSection::forHome()->get();
        }

        return view('store.index', compact('products', 'cartCount', 'canSeePrices', 'currentCategory', 'selectedCurrency', 'currencies', 'searchQuery', 'customerDiscountPercent', 'bannerSlides', 'homeSections'));
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
        return Currency::forCurrentUser();
    }

    /** Ürün detay sayfası — misafirler de görebilir */
    public function showProduct(Product $product): View
    {
        if (! $product->is_active) {
            abort(404);
        }
        $product->load(['company', 'category.parent', 'currency', 'variations.options', 'productImages']);
        $variations = $product->variations;
        $sorted = collect();
        $remaining = $variations->keyBy('id');
        while ($remaining->isNotEmpty()) {
            $added = $remaining->filter(function ($v) use ($sorted) {
                if (empty($v->depends_on)) {
                    return true;
                }
                return $sorted->contains('name', $v->depends_on);
            });
            if ($added->isEmpty()) {
                $sorted = $sorted->merge($remaining->values());
                break;
            }
            foreach ($added->values() as $v) {
                $sorted->push($v);
                $remaining->forget($v->id);
            }
        }
        $product->setRelation('variations', $sorted->values());
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

        return view('store.product', compact('product', 'canSeePrices', 'selectedCurrency', 'currencies', 'customerDiscountPercent', 'customerGroupId', 'sizeTables'));
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
                'image' => $p->image ? \Illuminate\Support\Facades\Storage::url($p->image) : null,
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
            return redirect()->back()->with('error', 'Bu ürün şu an satışa sunulmuyor.');
        }
        $availableStock = $product->getAvailableStock();
        if ($availableStock < 1) {
            return redirect()->back()->with('error', 'Bu ürün şu an stokta yok.');
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
                    return redirect()->back()->with('error', 'Bu ürün için minimum sipariş miktarı ' . $minOrder . ' adettir. Toplam beden adedi: ' . $qty);
                }
            }
        }
        if ($sizeQuantities === null) {
            $qty = (int) ($request->quantity ?: $minOrder);
        }
        if ($qty < $minOrder) {
            return redirect()->back()->with('error', 'Bu ürün için minimum sipariş miktarı ' . $minOrder . ' adettir.');
        }
        if ($qty > $availableStock) {
            return redirect()->back()->with('error', 'Yeterli stok yok. Maksimum ' . $availableStock . ' adet ekleyebilirsiniz.');
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
                return redirect()->back()->with('error', 'Bu ürünü sepete eklemek için lütfen tüm seçenekleri belirleyin (renk, beden vb.).');
            }
            foreach ($rootVariations as $variationName) {
                if (! isset($variationData[$variationName]) || (string) $variationData[$variationName] === '') {
                    return redirect()->back()->with('error', 'Lütfen "' . $variationName . '" seçeneğini belirleyin.');
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

        return redirect()->route('store.cart')->with('success', 'Ürün sepete eklendi.');
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
                        return redirect()->route('store.cart')->with('error', '"' . ($product?->name ?? 'Ürün') . '" için minimum sipariş miktarı ' . $minOrder . ' adettir.');
                    }
                    $cart[$key]['quantity'] = $qty;
                } else {
                    unset($cart[$key]);
                }
            }
        }
        session(['cart' => $cart]);

        return redirect()->route('store.cart')->with('success', 'Sepet güncellendi.');
    }

    public function removeFromCart(Request $request, string $cartKey)
    {
        $cart = $this->getCart();
        unset($cart[$cartKey]);
        session(['cart' => $cart]);

        return redirect()->route('store.cart')->with('success', 'Ürün sepetten çıkarıldı.');
    }

    public function checkout(): View|\Illuminate\Http\RedirectResponse
    {
        $cartItems = $this->getCartItems();
        if ($cartItems->isEmpty()) {
            return redirect()->route('home')->with('info', 'Sepetiniz boş.');
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
            return redirect()->route('home')->with('error', 'Sepetiniz boş.');
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

            $errorMessage = 'Sipariş oluşturulurken bir hata oluştu.';
            if (config('app.debug')) {
                $errorMessage .= ' ' . $e->getMessage();
            }

            return redirect()->route('store.checkout')->with('error', $errorMessage)->withInput();
        }

        return redirect()->route('store.order-confirmation', $order)->with('success', 'Siparişiniz alındı.');
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
            return redirect()->intended(route('home'))->with('success', 'Giriş yaptınız.');
        }

        return back()->withErrors(['email' => 'E-posta veya şifre hatalı.'])->withInput($request->only('email'))->with('open_login_modal', true);
    }

    /** Bayi başvuru sayfası: Neden Bayi + form. */
    public function dealerRegistrationPage(): View
    {
        return view('store.bayi-ol');
    }
}
