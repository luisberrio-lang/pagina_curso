<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class OrderService
{
    public function create(array $customer, CartService $cartService, string $checkoutToken, ?int $userId): Order
    {
        return DB::transaction(function () use ($customer, $cartService, $checkoutToken, $userId) {
            $cart = $cartService->snapshot(false, true);
            if ($cart['items'] === [] || $cart['invalid_ids'] !== []) {
                throw new RuntimeException('El carrito contiene productos no disponibles.');
            }

            $order = new Order;
            $usesFullName = array_key_exists('full_name', $customer);
            $order->forceFill([
                'order_number' => $this->newOrderNumber(),
                'public_token' => Str::random(64),
                'checkout_token_hash' => hash('sha256', $checkoutToken),
                'user_id' => $userId,
                'first_name' => $usesFullName ? $customer['full_name'] : $customer['first_name'],
                'last_name' => $usesFullName ? '' : $customer['last_name'],
                'email' => $customer['email'],
                'phone' => $customer['phone'],
                'document_type' => $usesFullName ? null : ($customer['document_type'] ?? null),
                'document_number' => $usesFullName ? null : ($customer['document_number'] ?? null),
                'subtotal' => $cart['subtotal'],
                'total' => $cart['total'],
                'currency' => $cart['currency'],
                'status' => Order::STATUS_PENDING,
                'payment_status' => Order::PAYMENT_PENDING,
            ])->save();

            foreach ($cart['items'] as $item) {
                $course = $item['course'];
                $itemModel = $order->items()->make();
                $itemModel->forceFill([
                    'course_id' => $course->getKey(),
                    'course_title' => $course->title,
                    'course_slug' => $course->slug,
                    'unit_price' => $item['unit_price'],
                    'currency' => $item['currency'],
                    'quantity' => 1,
                    'line_total' => $item['line_total'],
                ])->save();
            }

            return $order->load('items');
        });
    }

    private function newOrderNumber(): string
    {
        do {
            $number = 'ORD-'.now()->format('Y').'-'.strtoupper(Str::random(10));
        } while (Order::query()->where('order_number', $number)->exists());

        return $number;
    }
}
