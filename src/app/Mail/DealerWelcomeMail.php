<?php

namespace App\Mail;

use App\Models\DealerRequest;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class DealerWelcomeMail extends Mailable
{
    public function __construct(
        public DealerRequest $dealerRequest,
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
            subject: __('store.dealer_welcome_mail.subject', [
                'app' => config('app.name', 'Nevistanbul'),
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.dealer-welcome',
        );
    }
}
