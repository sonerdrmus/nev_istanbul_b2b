<?php

namespace App\Mail;

use App\Models\DealerRequest;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class DealerApprovedMail extends Mailable
{
    public function __construct(
        public DealerRequest $dealerRequest,
        public User $user,
        public ?string $generatedPassword = null,
        public string $loginUrl = '',
    ) {
        if ($this->loginUrl === '') {
            $this->loginUrl = route('store.login.show', [
                'locale' => config('store.default_locale', 'tr'),
            ]);
        }
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('store.dealer_mail.subject', [
                'app' => config('app.name', 'Nevistanbul'),
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.dealer-approved',
        );
    }
}
