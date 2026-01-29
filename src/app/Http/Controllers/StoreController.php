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
use App\Models\ProductVariation;
use App\Models\ShippingMethod;
use App\Models\ProductVariationOptionPrice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class StoreController extends Controller
{
    private function cartKey(int $productId, array $variations): string
    {
        if (empty($variations)) {
            return (string) $productId;
        }
        ksort($variations);

        return $productId . '_' . md5(json_encode($variations));
    }

    private function getCart(): array
    {
        return session('cart', []);
    }

    /** @return \Illuminate\Support\Collection<int, object{product: Product, quantity: int, subtotal: float, variations: array, cart_key: string}> */
    private function getCartItems(): \Illuminate\Support\Collection
    {
        $cart = $this->getCart();
        if (empty($cart)) {
            return collect();
        }
        $productIds = array_unique(array_column($cart, 'product_id'));
        $products = Product::with(['company', 'currency', 'variations.optionPrices'])->whereIn('id', $productIds)->get()->keyBy('id');
        $items = collect();
        foreach ($cart as $key => $item) {
            $product = $products->get($item['product_id'] ?? 0);
            if (! $product || ($item['quantity'] ?? 0) < 1) {
                continue;
            }
            $qty = (int) $item['quantity'];
            $variations = $item['variations'] ?? [];
            $productCurrency = $product->currency ?? Currency::getDefault();
            $customerGroupId = auth()->check() && auth()->user()->company?->customer_group_id
                ? (int) auth()->user()->company->customer_group_id
                : 1;

            // Ürün indirimi (miktar / müşteri grubu / tarih): varsa indirimli birim fiyat TL
            $discountUnitTry = $product->getDiscountUnitPriceInTRY($qty, $customerGroupId);
            $priceInTRY = $discountUnitTry !== null
                ? $discountUnitTry
                : ($productCurrency && $productCurrency->code !== 'TRY'
                    ? $productCurrency->convertToTRY((float) $product->price)
                    : (float) $product->price);

            $pricing = ProductVariationOptionPrice::forSelection($product, $variations);
            $deltaTry = (float) ($pricing['delta_total'] ?? 0);
            $breakdown = (array) ($pricing['breakdown'] ?? []);
            $unitTry = $priceInTRY + $deltaTry;

            // Müşteri kâr marjı (indirim) uygula
            $discountPercent = $this->getCustomerDiscountPercent();
            if ($discountPercent !== null && $discountPercent > 0) {
                $unitTry = $unitTry * (1 - $discountPercent / 100);
            }

            $items->push((object) [
                'product' => $product,
                'quantity' => $qty,
                'subtotal' => $unitTry * $qty, // TL cinsinden
                'variations' => $variations,
                'cart_key' => $key,
                'variation_price_delta_total' => $deltaTry,
                'variation_price_breakdown' => $breakdown,
                'unit_price_try' => $unitTry,
            ]);
        }

        return $items;
    }

    public function index(): View
    {
        $baseQuery = Product::with(['company', 'category.parent', 'currency', 'variations'])
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

        // Renk filtresi (varyasyon adı Renk veya Color, seçenek değeri)
        $filterColor = request('color');
        if ($filterColor !== null && $filterColor !== '') {
            $baseQuery->whereHas('variations', function ($q) use ($filterColor) {
                $q->whereIn('name', ['Renk', 'Color', 'Renk / Color'])
                    ->whereJsonContains('options', $filterColor);
            });
        }

        // Cinsiyet filtresi (varyasyon adı Cinsiyet, Erkek/Bayan vb., seçenek değeri)
        $filterCinsiyet = request('cinsiyet');
        if ($filterCinsiyet !== null && $filterCinsiyet !== '') {
            $baseQuery->whereHas('variations', function ($q) use ($filterCinsiyet) {
                $q->whereIn('name', ['Cinsiyet', 'Erkek/Bayan', 'Gender', 'Cinsiyet / Gender'])
                    ->whereJsonContains('options', $filterCinsiyet);
            });
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

        // Filtre paneli verileri: kategoriler (ağaç) ve firmalar (ürünü olan)
        $filterCategories = Category::with(['children' => fn ($q) => $q->active()->orderBy('sort_order')->orderBy('name')])
            ->active()
            ->roots()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        $filterCompanies = \App\Models\Company::whereHas('products', fn ($q) => $q->active()->visibleInStore())
            ->orderBy('name')
            ->get(['id', 'name']);

        // Renk ve Cinsiyet filtre seçenekleri (varyasyonlardan benzersiz değerler)
        $filterColors = ProductVariation::whereHas('product', fn ($q) => $q->active()->visibleInStore())
            ->whereIn('name', ['Renk', 'Color', 'Renk / Color'])
            ->get()
            ->pluck('options')
            ->flatten()
            ->unique()
            ->filter()
            ->sort()
            ->values();
        $filterCinsiyetler = ProductVariation::whereHas('product', fn ($q) => $q->active()->visibleInStore())
            ->whereIn('name', ['Cinsiyet', 'Erkek/Bayan', 'Gender', 'Cinsiyet / Gender'])
            ->get()
            ->pluck('options')
            ->flatten()
            ->unique()
            ->filter()
            ->sort()
            ->values();

        $currentFilters = array_filter([
            'category' => $categorySlug ?: ($parentSlug ?: null),
            'company' => $companyId ?: null,
            'in_stock' => request('in_stock') === '1' ? true : null,
            'status_satista' => $statusSatista === '1' ? true : null,
            'status_yakinda' => $statusYakinda === '1' ? true : null,
            'color' => $filterColor ?: null,
            'cinsiyet' => $filterCinsiyet ?: null,
            'q' => $searchQuery ?: null,
        ]);

        return view('store.index', compact('products', 'cartCount', 'canSeePrices', 'currentCategory', 'selectedCurrency', 'currencies', 'searchQuery', 'customerDiscountPercent', 'bannerSlides', 'homeSections', 'filterCategories', 'filterCompanies', 'filterColors', 'filterCinsiyetler', 'currentFilters'));
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
        $product->load(['company', 'category.parent', 'currency', 'variations']);
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

        return view('store.product', compact('product', 'canSeePrices', 'selectedCurrency', 'currencies', 'customerDiscountPercent', 'customerGroupId'));
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
            'variations' => 'nullable|array',
            'variations.*' => 'nullable|string|max:255',
        ]);
        $product = Product::with('variations.optionPrices')->findOrFail($request->product_id);
        if (! $product->isOnSale()) {
            return redirect()->back()->with('error', 'Bu ürün şu an satışa sunulmuyor.');
        }
        $variations = array_filter($request->input('variations', []));
        $availableStock = $product->getAvailableStock($variations);
        if ($availableStock < 1) {
            return redirect()->back()->with('error', 'Bu ürün şu an stokta yok.');
        }
        $minOrder = $product->getMinimumOrderQuantity();
        $qty = (int) ($request->quantity ?: $minOrder);
        if ($qty < $minOrder) {
            return redirect()->back()->with('error', 'Bu ürün için minimum sipariş miktarı ' . $minOrder . ' adettir.');
        }
        if ($qty > $availableStock) {
            return redirect()->back()->with('error', 'Yeterli stok yok. Maksimum ' . $availableStock . ' adet ekleyebilirsiniz.');
        }
        foreach ($product->variations as $v) {
            if ($v->type === 'select') {
                if (empty($variations[$v->name])) {
                    return redirect()->back()->with('error', "Lütfen \"{$v->name}\" seçin.");
                }
                if (! empty($v->depends_on)) {
                    $allowed = $v->getOptionsForParentValue($variations[$v->depends_on] ?? null);
                    if (! in_array($variations[$v->name], $allowed, true)) {
                        return redirect()->back()->with('error', "\"{$v->name}\" için geçersiz seçim.");
                    }
                }
            }
            if ($v->type === 'checkbox' && isset($variations[$v->name])) {
                $opts = empty($v->depends_on) ? ($v->options ?? []) : $v->getOptionsForParentValue($variations[$v->depends_on] ?? null);
                $variations[$v->name] = in_array($variations[$v->name], $opts, true) ? $variations[$v->name] : ($opts[0] ?? 'Var');
            }
        }
        $key = $this->cartKey((int) $product->id, $variations);
        $cart = $this->getCart();
        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $qty;
        } else {
            $cart[$key] = [
                'product_id' => $product->id,
                'quantity' => $qty,
                'variations' => $variations,
            ];
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
                $productCurrency = $item->product->currency ?? Currency::getDefault();
                // Ürün birim fiyatını TL'ye çevir
                $priceInTRY = $productCurrency && $productCurrency->code !== 'TRY'
                    ? $productCurrency->convertToTRY((float) $item->product->price)
                    : (float) $item->product->price;
                $deltaTry = (float) ($item->variation_price_delta_total ?? 0);
                $subtotal = (float) $item->subtotal;
                $variationData = ! empty($item->variations) ? (array) $item->variations : null;
                $breakdown = isset($item->variation_price_breakdown) && is_array($item->variation_price_breakdown)
                    ? $item->variation_price_breakdown
                    : null;
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product->id,
                    'product_name' => (string) $item->product->name,
                    'price' => round($priceInTRY + $deltaTry, 2),
                    'quantity' => (int) $item->quantity,
                    'subtotal' => round($subtotal, 2),
                    'variation_data' => $variationData,
                    'variation_price_delta_total' => round($deltaTry, 2),
                    'variation_price_breakdown' => $breakdown,
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
