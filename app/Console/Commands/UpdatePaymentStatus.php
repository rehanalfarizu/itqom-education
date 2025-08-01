<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;

class UpdatePaymentStatus extends Command
{
    protected $signature = 'payment:update {order_id} {status}';
    protected $description = 'Update payment status';

    public function handle()
    {
        $orderId = $this->argument('order_id');
        $status = $this->argument('status');
        
        $payment = Payment::where('order_id', $orderId)->first();
        
        if ($payment) {
            $this->info("Found payment: {$payment->order_id} with status: {$payment->status}");
            
            $payment->status = $status;
            $payment->save();
            
            $this->info("Updated payment status to: {$payment->status}");
        } else {
            $this->error("Payment not found");
        }
    }
}
