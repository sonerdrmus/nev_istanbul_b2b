<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Mail\ContactMessageMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Throwable;

class ContactMessageController extends Controller
{
    public function store(StoreContactMessageRequest $request): RedirectResponse
    {
        if (filled($request->input('website'))) {
            return redirect()
                ->route('store.contact')
                ->with('success', __('store.contact.sent'));
        }

        $to = (string) config('store.contact.to');

        if (! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            Log::error('Contact form mail recipient is not configured.');

            return back()
                ->withInput()
                ->with('error', __('store.contact.send_failed'));
        }

        $validated = $request->validated();
        $files = $request->file('attachments', []);

        if (! is_array($files)) {
            $files = $files ? [$files] : [];
        }

        try {
            Mail::to($to)->send(new ContactMessageMail(
                senderName: $validated['name'],
                senderEmail: $validated['email'],
                phone: $validated['phone'] ?? null,
                companyName: $validated['company'] ?? null,
                topic: $validated['subject'] ?? null,
                bodyText: $validated['message'],
                files: array_values($files),
            ));
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', __('store.contact.send_failed'));
        }

        return redirect()
            ->route('store.contact')
            ->with('success', __('store.contact.sent'));
    }
}
