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
        Schema::create('payment_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('type'); // reminder, overdue, payment_received, suspension_warning
            $table->string('title');
            $table->text('message');
            $table->string('status')->default('pending'); // pending, sent, failed, cancelled
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('scheduled_for')->nullable();
            $table->json('notification_data')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['type', 'status']);
            $table->index('scheduled_for');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_notifications');
    }
};
