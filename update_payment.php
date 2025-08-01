<?php

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Models\Payment;

// Find the payment and update its status
$payment = Payment::where('order_id', 'ORDER-1-1-688cab4d06e66')->first();

if ($payment) {
    echo "Found payment: " . $payment->order_id . " with status: " . $payment->status . "\n";
    
    $payment->status = 'success';
    $payment->save();
    
    echo "Updated payment status to: " . $payment->status . "\n";
} else {
    echo "Payment not found\n";
}
