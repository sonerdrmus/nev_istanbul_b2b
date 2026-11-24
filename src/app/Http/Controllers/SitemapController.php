<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\LegalPage;
use App\Models\Product;
use App\Support\StoreLocale;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $locales = StoreLocale::supported();
        $defaultLocale = StoreLocale::default();
        URL::defaults(['locale' => $defaultLocale]);
        $entries = [];

        $add = function (callable $locForLocale, ?string $lastmod = null, string $changefreq = 'weekly', string $priority = '0.5') use (&$entries, $locales): void {
            $alternates = [];
            foreach ($locales as $locale) {
                $alternates[$locale] = $locForLocale($locale);
            }
            $entries[] = [
                'loc' => $alternates[StoreLocale::default()] ?? reset($alternates),
                'lastmod' => $lastmod,
                'changefreq' => $changefreq,
                'priority' => $priority,
                'alternates' => $alternates,
            ];
        };

        $add(fn (string $locale) => route('home', ['locale' => $locale]), null, 'daily', '1.0');
        $add(fn (string $locale) => route('store.contact', ['locale' => $locale]), null, 'monthly', '0.6');
        $add(fn (string $locale) => route('store.dealer-registration', ['locale' => $locale]), null, 'monthly', '0.5');

        Category::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['slug', 'updated_at'])
            ->each(function (Category $category) use ($add): void {
                $add(
                    fn (string $locale) => route('home', ['locale' => $locale, 'category' => $category->slug]),
                    optional($category->updated_at)?->toAtomString(),
                    'weekly',
                    '0.8'
                );
            });

        Product::query()
            ->visibleInStore()
            ->where(function ($query): void {
                $query->whereNull('status')
                    ->orWhere('status', '!=', 'yakinda_gelecek');
            })
            ->orderBy('id')
            ->get(['id', 'slug', 'updated_at'])
            ->each(function (Product $product) use ($add): void {
                $add(
                    fn (string $locale) => route('store.product.show', ['locale' => $locale, 'product' => $product->slug]),
                    optional($product->updated_at)?->toAtomString(),
                    'weekly',
                    '0.7'
                );
            });

        BlogPost::query()
            ->published()
            ->orderByDesc('published_at')
            ->get(['slug', 'updated_at', 'published_at'])
            ->each(function (BlogPost $post) use ($add): void {
                $add(
                    fn (string $locale) => route('store.blog.show', ['locale' => $locale, 'post' => $post->slug]),
                    optional($post->updated_at ?? $post->published_at)?->toAtomString(),
                    'monthly',
                    '0.5'
                );
            });

        LegalPage::query()
            ->published()
            ->orderBy('slug')
            ->get(['slug', 'updated_at'])
            ->each(function (LegalPage $page) use ($add): void {
                $add(
                    fn (string $locale) => route('store.legal.show', ['locale' => $locale, 'slug' => $page->slug]),
                    optional($page->updated_at)?->toAtomString(),
                    'yearly',
                    '0.3'
                );
            });

        $xml = view('store.sitemap', ['urls' => $entries])->render();

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }
}
