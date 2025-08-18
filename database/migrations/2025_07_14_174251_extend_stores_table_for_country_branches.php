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
        Schema::table('stores', function (Blueprint $table) {
            // Vérifier si les colonnes n'existent pas déjà
            if (! Schema::hasColumn('stores', 'is_country_branch')) {
                $table->boolean('is_country_branch')->default(false);
            }

            if (! Schema::hasColumn('stores', 'country_code')) {
                $table->string('country_code', 3)->nullable();
            }

            if (! Schema::hasColumn('stores', 'country_name')) {
                $table->string('country_name')->nullable();
            }

            if (! Schema::hasColumn('stores', 'nif')) {
                $table->string('nif')->nullable();
            }

            if (! Schema::hasColumn('stores', 'rccm')) {
                $table->string('rccm')->nullable();
            }

            if (! Schema::hasColumn('stores', 'business_permit')) {
                $table->string('business_permit')->nullable();
            }

            if (! Schema::hasColumn('stores', 'tax_id')) {
                $table->string('tax_id')->nullable();
            }

            if (! Schema::hasColumn('stores', 'email')) {
                $table->string('email')->nullable();
            }

            if (! Schema::hasColumn('stores', 'website')) {
                $table->string('website')->nullable();
            }

            if (! Schema::hasColumn('stores', 'postal_box')) {
                $table->string('postal_box')->nullable();
            }

            if (! Schema::hasColumn('stores', 'invoice_header_image')) {
                $table->string('invoice_header_image')->nullable();
            }

            if (! Schema::hasColumn('stores', 'invoice_footer_image')) {
                $table->string('invoice_footer_image')->nullable();
            }
        });

        // Ajouter les index séparément en gérant les erreurs
        try {
            Schema::table('stores', function (Blueprint $table) {
                if (Schema::hasColumn('stores', 'is_country_branch')) {
                    $table->index('is_country_branch');
                }
            });
        } catch (\Exception $e) {
            // Index existe déjà, continuer
        }

        try {
            Schema::table('stores', function (Blueprint $table) {
                if (Schema::hasColumn('stores', 'country_code')) {
                    $table->index('country_code');
                }
            });
        } catch (\Exception $e) {
            // Index existe déjà, continuer
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropIndex(['is_country_branch']);
            $table->dropIndex(['country_code']);

            $table->dropColumn([
                'is_country_branch',
                'country_code',
                'country_name',
                'nif',
                'rccm',
                'business_permit',
                'tax_id',
                'email',
                'website',
                'postal_box',
                'invoice_header_image',
                'invoice_footer_image',
            ]);
        });
    }
};
