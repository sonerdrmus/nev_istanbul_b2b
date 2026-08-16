<?php

namespace App\Http\Controllers;

use App\Models\DealerRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DealerRequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:dealer_requests,email'],
            'phone' => ['required', 'string', 'max:64'],
            'mobile_phone' => ['nullable', 'string', 'max:64'],
            'business_name' => ['required', 'string', 'max:255'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'postcode' => ['required', 'string', 'max:32'],
            'country' => ['required', 'string', 'max:120'],
            'different_delivery_address' => ['sometimes', 'boolean'],
            'delivery_address_line_1' => ['required_if:different_delivery_address,1', 'nullable', 'string', 'max:255'],
            'delivery_address_line_2' => ['nullable', 'string', 'max:255'],
            'delivery_city' => ['required_if:different_delivery_address,1', 'nullable', 'string', 'max:120'],
            'delivery_postcode' => ['required_if:different_delivery_address,1', 'nullable', 'string', 'max:32'],
            'delivery_country' => ['required_if:different_delivery_address,1', 'nullable', 'string', 'max:120'],
            'business_type' => ['required', 'string', 'max:255'],
            'limited_company_name' => ['nullable', 'string', 'max:255'],
            'company_registration_number' => ['nullable', 'string', 'max:128'],
            'vat_reg_number' => ['nullable', 'string', 'max:128'],
            'website' => ['nullable', 'string', 'max:2048'],
            'facebook' => ['nullable', 'string', 'max:2048'],
            'instagram' => ['nullable', 'string', 'max:2048'],
            'twitter' => ['nullable', 'string', 'max:2048'],
            'linkedin' => ['nullable', 'string', 'max:2048'],
            'business_profile' => ['required', 'string', Rule::in(DealerRequest::BUSINESS_PROFILES)],
            'interest_areas' => ['required', 'array', 'min:1'],
            'interest_areas.*' => ['string', Rule::in(DealerRequest::INTEREST_AREA_KEYS)],
            'how_heard_about_us' => ['required', 'string', 'max:500'],
            'terms_accepted' => ['accepted'],
        ]);

        $fullName = trim($validated['first_name'].' '.$validated['last_name']);

        $differentDelivery = $request->boolean('different_delivery_address');

        DealerRequest::create([
            'full_name' => $fullName,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'tc_no' => null,
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'mobile_phone' => $validated['mobile_phone'] ?? null,
            'address' => null,
            'business_name' => $validated['business_name'],
            'address_line_1' => $validated['address_line_1'],
            'address_line_2' => $validated['address_line_2'] ?? null,
            'city' => $validated['city'],
            'postcode' => $validated['postcode'],
            'country' => $validated['country'],
            'different_delivery_address' => $differentDelivery,
            'delivery_address_line_1' => $differentDelivery ? ($validated['delivery_address_line_1'] ?? null) : null,
            'delivery_address_line_2' => $differentDelivery ? ($validated['delivery_address_line_2'] ?? null) : null,
            'delivery_city' => $differentDelivery ? ($validated['delivery_city'] ?? null) : null,
            'delivery_postcode' => $differentDelivery ? ($validated['delivery_postcode'] ?? null) : null,
            'delivery_country' => $differentDelivery ? ($validated['delivery_country'] ?? null) : null,
            'business_type' => $validated['business_type'],
            'limited_company_name' => $validated['limited_company_name'] ?? null,
            'company_registration_number' => $validated['company_registration_number'] ?? null,
            'vat_reg_number' => $validated['vat_reg_number'] ?? null,
            'website' => $validated['website'] ?? null,
            'facebook' => $validated['facebook'] ?? null,
            'instagram' => $validated['instagram'] ?? null,
            'twitter' => $validated['twitter'] ?? null,
            'linkedin' => $validated['linkedin'] ?? null,
            'business_profile' => $validated['business_profile'],
            'interest_areas' => array_values(array_unique($validated['interest_areas'])),
            'how_heard_about_us' => $validated['how_heard_about_us'],
            'terms_accepted' => true,
            'status' => 'pending',
        ]);

        return redirect()->route('home')
            ->with('success', __('store.dealer.request_received'))
            ->with('show_dealer_success_modal', true);
    }
}
