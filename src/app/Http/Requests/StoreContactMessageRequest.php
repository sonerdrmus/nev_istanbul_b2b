<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $maxAttachments = (int) config('store.contact.max_attachments', 5);
        $maxKilobytes = (int) config('store.contact.max_kilobytes', 8192);

        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
            'company' => ['nullable', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:180'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'attachments' => ['nullable', 'array', 'max:'.$maxAttachments],
            'attachments.*' => [
                'file',
                'max:'.$maxKilobytes,
                'mimes:pdf,jpg,jpeg,png,webp,doc,docx,xls,xlsx,zip',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('store.contact.name'),
            'email' => __('store.contact.email'),
            'phone' => __('store.contact.phone'),
            'company' => __('store.contact.company'),
            'subject' => __('store.contact.subject'),
            'message' => __('store.contact.message'),
            'attachments' => __('store.contact.attachments'),
            'attachments.*' => __('store.contact.attachments'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return parent::getRedirectUrl().'#contact-form';
    }
}
