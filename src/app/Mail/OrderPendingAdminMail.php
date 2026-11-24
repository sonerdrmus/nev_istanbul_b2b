<?php

namespace App\Mail;

use App\Models\Order;
use App\Support\OrderPendingMailData;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class OrderPendingAdminMail extends Mailable
{
    public function __construct(public Order $order)
    {
        // Yönetici e-postaları her zaman Türkçe gönderilir.
        $this->locale('tr');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('store.order_mail.admin_subject', [
                'number' => $this->order->order_number,
                'name' => $this->order->customer_name,
            ], 'tr'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-pending-admin',
            with: [
                'order' => $this->order,
                'items' => OrderPendingMailData::items($this->order),
                'statusLabel' => OrderPendingMailData::statusLabel($this->order),
                'adminUrl' => OrderPendingMailData::adminOrderUrl($this->order),
                'totalFormatted' => OrderPendingMailData::money((float) $this->order->total),
                'shippingFormatted' => OrderPendingMailData::money((float) $this->order->shipping_cost),
            ],
        );
    }
}
