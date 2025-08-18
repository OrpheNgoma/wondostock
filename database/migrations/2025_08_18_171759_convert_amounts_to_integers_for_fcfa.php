<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convertir les montants FCFA des decimaux vers des entiers
        // Les FCFA n'ont pas de sous-unité, donc pas de multiplication par 100

        // Table: products
        Schema::table('products', function (Blueprint $table) {
            $table->integer('purchase_price_new')->after('purchase_price')->default(0);
            $table->integer('selling_price_new')->after('selling_price')->default(0);
        });

        // Convertir les données existantes (round pour éviter les erreurs de précision)
        DB::statement('UPDATE products SET purchase_price_new = ROUND(purchase_price), selling_price_new = ROUND(selling_price)');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['purchase_price', 'selling_price']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('purchase_price_new', 'purchase_price');
            $table->renameColumn('selling_price_new', 'selling_price');
        });

        // Table: documents
        Schema::table('documents', function (Blueprint $table) {
            $table->integer('sub_total_new')->after('sub_total')->default(0);
            $table->integer('tax_amount_new')->after('tax_amount')->default(0);
            $table->integer('total_amount_new')->after('total_amount')->default(0);
            $table->integer('paid_amount_new')->after('paid_amount')->default(0);
        });

        DB::statement('UPDATE documents SET sub_total_new = ROUND(sub_total), tax_amount_new = ROUND(tax_amount), total_amount_new = ROUND(total_amount), paid_amount_new = ROUND(paid_amount)');

        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['sub_total', 'tax_amount', 'total_amount', 'paid_amount']);
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->renameColumn('sub_total_new', 'sub_total');
            $table->renameColumn('tax_amount_new', 'tax_amount');
            $table->renameColumn('total_amount_new', 'total_amount');
            $table->renameColumn('paid_amount_new', 'paid_amount');
        });

        // Table: document_items
        Schema::table('document_items', function (Blueprint $table) {
            $table->integer('unit_price_new')->after('unit_price')->default(0);
            $table->integer('total_amount_new')->after('total_amount')->default(0);
        });

        DB::statement('UPDATE document_items SET unit_price_new = ROUND(unit_price), total_amount_new = ROUND(total_amount)');

        Schema::table('document_items', function (Blueprint $table) {
            $table->dropColumn(['unit_price', 'total_amount']);
        });

        Schema::table('document_items', function (Blueprint $table) {
            $table->renameColumn('unit_price_new', 'unit_price');
            $table->renameColumn('total_amount_new', 'total_amount');
        });

        // Table: payments
        Schema::table('payments', function (Blueprint $table) {
            $table->integer('amount_new')->after('amount')->default(0);
        });

        DB::statement('UPDATE payments SET amount_new = ROUND(amount)');

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('amount');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('amount_new', 'amount');
        });

        // Table: plans (pour la facturation SaaS)
        Schema::table('plans', function (Blueprint $table) {
            $table->integer('price_new')->after('price')->default(0);
        });

        DB::statement('UPDATE plans SET price_new = ROUND(price)');

        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('price');
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->renameColumn('price_new', 'price');
        });

        // Table: invoices (facturation SaaS)
        Schema::table('invoices', function (Blueprint $table) {
            $table->integer('amount_new')->after('amount')->default(0);
            $table->integer('tax_amount_new')->after('tax_amount')->default(0);
            $table->integer('total_amount_new')->after('total_amount')->default(0);
        });

        DB::statement('UPDATE invoices SET amount_new = ROUND(amount), tax_amount_new = ROUND(tax_amount), total_amount_new = ROUND(total_amount)');

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['amount', 'tax_amount', 'total_amount']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->renameColumn('amount_new', 'amount');
            $table->renameColumn('tax_amount_new', 'tax_amount');
            $table->renameColumn('total_amount_new', 'total_amount');
        });

        // Table: billing_payments
        Schema::table('billing_payments', function (Blueprint $table) {
            $table->integer('amount_new')->after('amount')->default(0);
        });

        DB::statement('UPDATE billing_payments SET amount_new = ROUND(amount)');

        Schema::table('billing_payments', function (Blueprint $table) {
            $table->dropColumn('amount');
        });

        Schema::table('billing_payments', function (Blueprint $table) {
            $table->renameColumn('amount_new', 'amount');
        });

        // Table: metrics (pour les valeurs monétaires dans les métriques)
        DB::statement('UPDATE metrics SET value = ROUND(value) WHERE value IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurer les types decimal (en cas de rollback)

        // Table: products
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('purchase_price_old', 15, 3)->after('purchase_price')->default(0);
            $table->decimal('selling_price_old', 15, 3)->after('selling_price')->default(0);
        });

        DB::statement('UPDATE products SET purchase_price_old = purchase_price, selling_price_old = selling_price');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['purchase_price', 'selling_price']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('purchase_price_old', 'purchase_price');
            $table->renameColumn('selling_price_old', 'selling_price');
        });

        // Table: documents
        Schema::table('documents', function (Blueprint $table) {
            $table->decimal('sub_total_old', 15, 3)->after('sub_total')->default(0);
            $table->decimal('tax_amount_old', 15, 3)->after('tax_amount')->default(0);
            $table->decimal('total_amount_old', 15, 3)->after('total_amount')->default(0);
            $table->decimal('paid_amount_old', 15, 3)->after('paid_amount')->default(0);
        });

        DB::statement('UPDATE documents SET sub_total_old = sub_total, tax_amount_old = tax_amount, total_amount_old = total_amount, paid_amount_old = paid_amount');

        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['sub_total', 'tax_amount', 'total_amount', 'paid_amount']);
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->renameColumn('sub_total_old', 'sub_total');
            $table->renameColumn('tax_amount_old', 'tax_amount');
            $table->renameColumn('total_amount_old', 'total_amount');
            $table->renameColumn('paid_amount_old', 'paid_amount');
        });

        // Continue with other tables...
        // (Similar pattern for document_items, payments, plans, invoices, billing_payments)
    }
};
