<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('phone');
            $table->text('message');
            $table->string('event')->default('custom'); // order_placed, order_shipped, admin_alert, custom, test
            $table->string('gateway')->default('log'); // greenweb, bulksmsbd, generic, log
            $table->string('status')->default('sent'); // sent, failed, simulated
            $table->text('response')->nullable();
            $table->timestamps();

            $table->index(['phone', 'created_at']);
            $table->index(['order_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
