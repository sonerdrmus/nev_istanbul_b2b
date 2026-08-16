<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealerRequest extends Model
{
    public const BUSINESS_PROFILES = [
        'agency',
        'end_user',
        'wholesaler',
        'commission_agent',
    ];

    public const INTEREST_AREA_KEYS = [
        'fashion',
        'sports',
        'workwear',
        'corporate',
        'hospitality',
        'education',
        'outerwear',
        'merchandise',
        'gifting',
        'other',
    ];

    /** @var array<string, string> */
    public const BUSINESS_PROFILE_LABELS = [
        'agency' => 'Ajans',
        'end_user' => 'Nihai Kullanıcı',
        'wholesaler' => 'Toptancı',
        'commission_agent' => 'Komisyoncu',
        // Eski başvurular
        'printer' => 'Matbaa / baskı',
        'embroiderer' => 'Nakış',
        'garment_manufacturer' => 'Konfeksiyon / giysi üreticisi',
        'promotional_product_distributor' => 'Promosyon ürün distribütörü',
        'retailer' => 'Perakendeci',
        'other' => 'Diğer',
    ];

    /** @var array<string, string> */
    public const INTEREST_AREA_LABELS = [
        'fashion' => 'Moda',
        'sports' => 'Spor',
        'workwear' => 'İş kıyafeti',
        'corporate' => 'Kurumsal',
        'hospitality' => 'Otel & ikram',
        'education' => 'Eğitim',
        'outerwear' => 'Dış giyim',
        'merchandise' => 'Promosyon ürünleri',
        'gifting' => 'Hediyelik',
        'other' => 'Diğer',
    ];

    protected $fillable = [
        'full_name',
        'first_name',
        'last_name',
        'tc_no',
        'email',
        'phone',
        'mobile_phone',
        'address',
        'business_name',
        'address_line_1',
        'address_line_2',
        'city',
        'postcode',
        'country',
        'different_delivery_address',
        'delivery_address_line_1',
        'delivery_address_line_2',
        'delivery_city',
        'delivery_postcode',
        'delivery_country',
        'business_type',
        'limited_company_name',
        'company_registration_number',
        'vat_reg_number',
        'website',
        'facebook',
        'instagram',
        'twitter',
        'linkedin',
        'business_profile',
        'interest_areas',
        'how_heard_about_us',
        'terms_accepted',
        'document_pdf_path',
        'document_jpeg_path',
        'status',
        'approved_at',
        'approved_by',
        'created_company_id',
        'created_user_id',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'interest_areas' => 'array',
            'terms_accepted' => 'boolean',
            'different_delivery_address' => 'boolean',
        ];
    }

    public function applicantDisplayName(): string
    {
        $fromParts = trim((string) $this->first_name.' '.(string) $this->last_name);

        return $fromParts !== '' ? $fromParts : (string) $this->full_name;
    }

    public function businessProfileLabel(): ?string
    {
        $key = $this->business_profile;

        return $key ? (self::BUSINESS_PROFILE_LABELS[$key] ?? $key) : null;
    }

    public function interestAreasLabelled(): string
    {
        $keys = $this->interest_areas ?? [];
        if (! is_array($keys) || $keys === []) {
            return '';
        }

        return collect($keys)
            ->map(fn ($k) => self::INTEREST_AREA_LABELS[(string) $k] ?? (string) $k)
            ->implode(', ');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdCompany()
    {
        return $this->belongsTo(Company::class, 'created_company_id');
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }
}

