<?php

namespace App\Support;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Bekleyen sipariş e-postaları için ortak veri hazırlığı.
 */
class OrderPendingMailData
{
    /**
     * @return list<array{
     *     name: string,
     *     quantity: int,
     *     unit_price: string,
     *     subtotal: string,
     *     lines: list<string>,
     *     quick_notes: string,
     *     quick_images: list<array{url: string}>
     * }>
     */
    public static function items(Order $order): array
    {
        $order->loadMissing('items');
        $items = [];

        foreach ($order->items as $item) {
            $data = is_array($item->variation_data) ? $item->variation_data : [];
            $quick = is_array($data['quick_order'] ?? null) ? $data['quick_order'] : [];

            $lines = [];
            foreach ($data as $name => $value) {
                if (in_array((string) $name, [
                    'quick_order',
                    'size_quantities',
                    'product_customization',
                    'product_customization_table',
                    'product_customization_notes',
                ], true)) {
                    continue;
                }
                foreach (VariationSelectionDisplay::rows($value) as $row) {
                    if (! empty($row['divider']) || ($row['value'] ?? '') === '') {
                        continue;
                    }
                    $lines[] = (($row['label'] ?? '') !== '' ? $row['label'].': ' : '').$row['value'];
                }
                if (count($lines) >= 12) {
                    break;
                }
            }

            if (isset($data['size_quantities']) && is_array($data['size_quantities'])) {
                $sizes = [];
                foreach ($data['size_quantities'] as $size => $qty) {
                    if ((int) $qty > 0) {
                        $sizes[] = $size.': '.(int) $qty;
                    }
                }
                if ($sizes !== []) {
                    $lines[] = __('store.order_confirmation.size_breakdown').' '.implode(', ', $sizes);
                }
            }

            $items[] = [
                'name' => (string) $item->product_name,
                'quantity' => (int) $item->quantity,
                'unit_price' => self::money((float) $item->price),
                'subtotal' => self::money((float) $item->subtotal),
                'lines' => $lines,
                'quick_notes' => trim((string) ($quick['notes'] ?? '')),
                'quick_images' => array_map(
                    fn (array $image): array => ['url' => $image['url']],
                    QuickOrderImages::absoluteList($quick)
                ),
            ];
        }

        return $items;
    }

    public static function money(float $amount): string
    {
        return number_format($amount, 2, ',', '.').' ₺';
    }

    public static function statusLabel(Order $order): string
    {
        return $order->status === 'pending'
            ? __('store.order_mail.status_pending')
            : (string) $order->status;
    }

    public static function adminOrderUrl(Order $order): string
    {
        try {
            return \App\Filament\Resources\OrderResource::getUrl('view', ['record' => $order]);
        } catch (\Throwable $e) {
            return url('/'.ltrim((string) config('app.admin_panel_path'), '/'));
        }
    }

    /**
     * Yönetici alıcıları: iletişim adresi + is_admin kullanıcıları (tekil, geçerli e-posta).
     *
     * @return list<string>
     */
    public static function adminRecipients(): array
    {
        $emails = [];
        $contact = trim((string) config('store.contact.to'));
        if ($contact !== '') {
            foreach (preg_split('/[;,]+/', $contact) ?: [] as $part) {
                $emails[] = trim($part);
            }
        }

        try {
            $adminEmails = User::query()
                ->where('is_admin', true)
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->pluck('email')
                ->all();
            foreach ($adminEmails as $email) {
                $emails[] = trim((string) $email);
            }
        } catch (\Throwable $e) {
            Log::warning('Admin e-posta alıcıları okunamadı', ['message' => $e->getMessage()]);
        }

        $unique = [];
        foreach ($emails as $email) {
            if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            $unique[mb_strtolower($email)] = $email;
        }

        return array_values($unique);
    }
}
