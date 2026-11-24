@php
    $orgName = __('store.footer.company_name');
    $brandName = __('store.meta_suffix');
    $phone = trim((string) config('store.whatsapp.number', __('store.footer.company_phone')));
    $phoneDigits = preg_replace('/\D+/', '', $phone) ?: '';
    if (str_starts_with($phoneDigits, '0') && strlen($phoneDigits) === 11) {
        $phoneDigits = '90'.substr($phoneDigits, 1);
    }
    $sameAs = array_values(array_filter([
        config('store.social.instagram'),
        config('store.social.linkedin'),
        config('store.social.facebook'),
    ]));
    $org = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Organization',
                '@id' => url('/').'#organization',
                'name' => $orgName,
                'alternateName' => $brandName,
                'url' => url('/'),
                'logo' => asset('images/logo.png'),
                'email' => __('store.footer.company_email'),
                'telephone' => $phoneDigits !== '' ? '+'.$phoneDigits : null,
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => '15 Temmuz Mahallesi 1432 Sokak No:26-30',
                    'addressLocality' => 'Bağcılar',
                    'addressRegion' => 'İstanbul',
                    'addressCountry' => 'TR',
                ],
                'sameAs' => $sameAs !== [] ? $sameAs : null,
            ],
            [
                '@type' => 'WebSite',
                '@id' => url('/').'#website',
                'url' => url('/'),
                'name' => $brandName,
                'publisher' => ['@id' => url('/').'#organization'],
                'inLanguage' => array_values(config('store.locales', ['tr', 'en', 'it'])),
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => [
                        '@type' => 'EntryPoint',
                        'urlTemplate' => route('home', ['locale' => app()->getLocale()]).'?q={search_term_string}',
                    ],
                    'query-input' => 'required name=search_term_string',
                ],
            ],
        ],
    ];
    $org['@graph'] = array_map(static function (array $node): array {
        return array_filter($node, static fn ($v) => $v !== null && $v !== []);
    }, $org['@graph']);
@endphp
<script type="application/ld+json">{!! json_encode($org, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
