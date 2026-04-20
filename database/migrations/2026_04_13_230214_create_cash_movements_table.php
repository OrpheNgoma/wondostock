<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('session_id')->constrained('cash_register_sessions')->cascadeOnDelete();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('expense_id')->nullable()->constrained('expenses')->nullOnDelete();
            $table->string('type', 20); // cash_in, cash_out, remittance
            $table->integer('amount');
            $table->string('label');
            $table->date('movement_date');
            $table->timestamps();

            $table->index('company_id');
            $table->index('session_id');
            $table->index('expense_id');
        });

        // Migrer les données existantes : pour chaque session ayant des valeurs non nulles,
        // on crée un mouvement historique par type.
        $sessions = DB::table('cash_register_sessions')
            ->where(function ($q) {
                $q->where('cash_in', '>', 0)
                    ->orWhere('cash_out', '>', 0)
                    ->orWhere('remittances', '>', 0);
            })
            ->get();

        $now = now();

        foreach ($sessions as $session) {
            if ($session->cash_in > 0) {
                DB::table('cash_movements')->insert([
                    'company_id' => $session->company_id,
                    'session_id' => $session->id,
                    'store_id' => $session->store_id,
                    'user_id' => $session->user_id,
                    'expense_id' => null,
                    'type' => 'cash_in',
                    'amount' => $session->cash_in,
                    'label' => 'Import historique — encaissements',
                    'movement_date' => $session->session_date,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            if ($session->cash_out > 0) {
                DB::table('cash_movements')->insert([
                    'company_id' => $session->company_id,
                    'session_id' => $session->id,
                    'store_id' => $session->store_id,
                    'user_id' => $session->user_id,
                    'expense_id' => null,
                    'type' => 'cash_out',
                    'amount' => $session->cash_out,
                    'label' => 'Import historique — sorties',
                    'movement_date' => $session->session_date,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            if ($session->remittances > 0) {
                DB::table('cash_movements')->insert([
                    'company_id' => $session->company_id,
                    'session_id' => $session->id,
                    'store_id' => $session->store_id,
                    'user_id' => $session->user_id,
                    'expense_id' => null,
                    'type' => 'remittance',
                    'amount' => $session->remittances,
                    'label' => 'Import historique — versements DG',
                    'movement_date' => $session->session_date,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_movements');
    }
};
