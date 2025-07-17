<?php

namespace App\Livewire\Reports;

use App\Models\Document;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Rapports - WondoStock')]
class Index extends Component
{
    /**
     * Exporte le journal des ventes au format CSV.
     */
    public function exportSales()
    {
        $companyId = Auth::user()->company_id;
        $documents = Document::where('company_id', $companyId)
            ->whereIn('type', ['invoice', 'credit_note'])
            ->with(['customer', 'items'])
            ->latest('document_date')->get();

        $fileName = 'journal_des_ventes_'.now()->format('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($documents) {
            $file = fopen('php://output', 'w');
            // En-têtes du CSV
            fputcsv($file, ['Date', 'Numero', 'Type', 'Client', 'Produit', 'Quantite', 'Prix Unitaire HT', 'Total HT']);

            foreach ($documents as $doc) {
                foreach ($doc->items as $item) {
                    fputcsv($file, [
                        $doc->document_date->format('d/m/Y'),
                        $doc->document_number,
                        $doc->type->label(),
                        $doc->customer->name,
                        $item->description,
                        $item->quantity,
                        $item->unit_price,
                        $item->quantity * $item->unit_price,
                    ]);
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Exporte l'état actuel des stocks au format CSV.
     */
    public function exportStockState()
    {
        $companyId = Auth::user()->company_id;
        $products = Product::where('company_id', $companyId)
            ->with('stores')
            ->orderBy('name')->get();

        $fileName = 'etat_des_stocks_'.now()->format('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Produit', 'SKU', 'Magasin', 'Quantite en Stock', 'Valeur du Stock (Prix Achat)']);

            foreach ($products as $product) {
                foreach ($product->stores as $store) {
                    fputcsv($file, [
                        $product->name,
                        $product->sku,
                        $store->name,
                        $store->pivot->quantity,
                        $store->pivot->quantity * $product->purchase_price,
                    ]);
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Exporte l'historique des paiements au format CSV.
     */
    public function exportPayments()
    {
        $companyId = Auth::user()->company_id;
        $payments = Payment::where('company_id', $companyId)
            ->with(['invoice', 'user'])
            ->latest('payment_date')->get();

        $fileName = 'historique_paiements_'.now()->format('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($payments) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date Paiement', 'Facture N°', 'Montant', 'Methode', 'Enregistre par']);

            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->payment_date->format('d/m/Y'),
                    $payment->invoice->document_number,
                    $payment->amount,
                    $payment->payment_method->label(),
                    $payment->user->name,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        return view('livewire.reports.index');
    }
}
