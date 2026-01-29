<?php

namespace App\Http\Controllers;

use App\Models\DealerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DealerRequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'tc_no' => ['required', 'string', 'size:11', 'unique:dealer_requests,tc_no'],
            'email' => ['required', 'email', 'max:255', 'unique:dealer_requests,email'],
            'phone' => ['nullable', 'string', 'max:32'],
            'address' => ['nullable', 'string', 'max:5000'],
            'document_pdf' => ['nullable', 'file', 'mimetypes:application/pdf', 'max:5120'],
            'document_jpeg' => ['nullable', 'file', 'mimetypes:image/jpeg', 'max:5120'],
        ]);

        $dealerRequest = DealerRequest::create([
            'full_name' => $validated['full_name'],
            'tc_no' => $validated['tc_no'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'status' => 'pending',
        ]);

        $updates = [];
        if ($request->hasFile('document_pdf')) {
            $updates['document_pdf_path'] = $request->file('document_pdf')->storePublicly("dealer_requests/{$dealerRequest->id}", 'public');
        }
        if ($request->hasFile('document_jpeg')) {
            $updates['document_jpeg_path'] = $request->file('document_jpeg')->storePublicly("dealer_requests/{$dealerRequest->id}", 'public');
        }
        if ($updates) {
            $dealerRequest->update($updates);
        }

        return redirect()->route('home')
            ->with('success', 'Bayilik talebiniz alındı. İnceleme sonrası sizinle iletişime geçilecektir.')
            ->with('show_dealer_success_modal', true);
    }
}

