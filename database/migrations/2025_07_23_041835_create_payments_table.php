
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_profile_id')->constrained('users_profile')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('course_description')->onDelete('cascade');
            $table->string('order_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'success', 'failed', 'cancelled', 'expired', 'challenge'])->default('pending');
            $table->string('snap_token')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('transaction_id')->nullable();
            $table->timestamp('transaction_time')->nullable();
            $table->string('transaction_status')->nullable();
            $table->string('fraud_status')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['user_profile_id', 'status']);
            $table->index(['course_id', 'status']);
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
