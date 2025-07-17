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
        // Index pour optimiser les recherches de produits
        Schema::table('products', function (Blueprint $table) {
            $table->index(['company_id', 'name'], 'products_company_name_idx');
            $table->index(['company_id', 'sku'], 'products_company_sku_idx');
            $table->index(['company_id', 'is_active'], 'products_company_active_idx');
            $table->index(['company_id', 'category_id'], 'products_company_category_idx');
        });

        // Index pour optimiser les requêtes de documents (factures, etc.)
        Schema::table('documents', function (Blueprint $table) {
            $table->index(['company_id', 'type', 'status'], 'documents_company_type_status_idx');
            $table->index(['company_id', 'customer_id'], 'documents_company_customer_idx');
            $table->index(['company_id', 'document_date'], 'documents_company_date_idx');
            $table->index(['company_id', 'store_id'], 'documents_company_store_idx');
        });

        // Index pour optimiser les mouvements de stock
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->index(['product_id', 'store_id', 'created_at'], 'stock_movements_product_store_date_idx');
            $table->index(['company_id', 'type'], 'stock_movements_company_type_idx');
            $table->index(['company_id', 'created_at'], 'stock_movements_company_date_idx');
        });

        // Index pour les items de documents (performance des calculs Dashboard)
        Schema::table('document_items', function (Blueprint $table) {
            $table->index(['document_id', 'product_id'], 'document_items_doc_product_idx');
            $table->index(['product_id'], 'document_items_product_idx');
        });

        // Index pour les clients
        Schema::table('customers', function (Blueprint $table) {
            $table->index(['company_id', 'name'], 'customers_company_name_idx');
            $table->index(['company_id', 'created_at'], 'customers_company_date_idx');
        });

        // Index pour les catégories
        Schema::table('categories', function (Blueprint $table) {
            $table->index(['company_id', 'name'], 'categories_company_name_idx');
        });

        // Index pour les magasins
        Schema::table('stores', function (Blueprint $table) {
            $table->index(['company_id', 'is_active'], 'stores_company_active_idx');
        });

        // Index pour les transferts de stock
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->index(['company_id', 'status'], 'stock_transfers_company_status_idx');
            $table->index(['from_store_id'], 'stock_transfers_from_store_idx');
            $table->index(['to_store_id'], 'stock_transfers_to_store_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_company_name_idx');
            $table->dropIndex('products_company_sku_idx');
            $table->dropIndex('products_company_active_idx');
            $table->dropIndex('products_company_category_idx');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex('documents_company_type_status_idx');
            $table->dropIndex('documents_company_customer_idx');
            $table->dropIndex('documents_company_date_idx');
            $table->dropIndex('documents_company_store_idx');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropIndex('stock_movements_product_store_date_idx');
            $table->dropIndex('stock_movements_company_type_idx');
            $table->dropIndex('stock_movements_company_date_idx');
        });

        Schema::table('document_items', function (Blueprint $table) {
            $table->dropIndex('document_items_doc_product_idx');
            $table->dropIndex('document_items_product_idx');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('customers_company_name_idx');
            $table->dropIndex('customers_company_date_idx');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_company_name_idx');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->dropIndex('stores_company_active_idx');
        });

        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->dropIndex('stock_transfers_company_status_idx');
            $table->dropIndex('stock_transfers_from_store_idx');
            $table->dropIndex('stock_transfers_to_store_idx');
        });
    }
};
