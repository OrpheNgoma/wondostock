<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->nullable()->after('user_id');
            $table->string('agent', 150)->nullable()->after('label');
            $table->string('receipt_number', 100)->nullable()->after('agent');

            $table->foreign('store_id')->references('id')->on('stores')->nullOnDelete();
            $table->index('store_id');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropIndex(['store_id']);
            $table->dropColumn(['store_id', 'agent', 'receipt_number']);
        });
    }
};
