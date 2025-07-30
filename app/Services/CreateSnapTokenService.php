<?php

namespace App\Services;

use Midtrans\Snap;
use Midtrans\Config;

class CreateSnapTokenService
{
    protected $order;

    public function __construct($order)
    {
        $this->order = $order;

        // Ambil dari config/midtrans.php
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function getSnapToken()
    {
        $params = [
            'transaction_details' => [
                'order_id' => $this->order->order_id,
                'gross_amount' => (int) $this->order->amount,
            ],
            'customer_details' => [
                'first_name' => $this->order->user->name ?? 'Customer',
                'email' => $this->order->user->email ?? 'email@example.com',
            ],
        ];

        return Snap::getSnapToken($params);
    }
}
