<?php

namespace App\Mail;

use App\Models\Order;
use App\Support\OrderPendingMailData;
use App\Support\StoreLocale;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class OrderPendingCustomerMail extends Mailable
{
    public function __construct(public Order $order)
    {
        $locale = StoreLocale::isSupported((string) $order->locale) ? (string) $order->locale : 'tr';
        $this->locale($locale);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('store.order_mail.customer_subject', [
                'number' => $this->order->order_number,
                'app' => config('app.name', 'Nevistanbul'),
            ], $this->locale),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-pending-customer',
            with: [
                'order' => $this->order,
                'items' => OrderPendingMailData::items($this->order),
                'statusLabel' => OrderPendingMailData::statusLabel($this->order),
                'totalFormatted' => OrderPendingMailData::money((float) $this->order->total),
                'shippingFormatted' => OrderPendingMailData::money((float) $this->order->shipping_cost),
            ],
        );
    }
}
