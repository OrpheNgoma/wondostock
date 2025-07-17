<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorSVG;

class ProductBarcodeController extends Controller
{
    /**
     * Génère et retourne une image de code-barres pour un SKU donné.
     */
    public function __invoke(Request $request, $sku)
    {
        try {
            // Valider que l'utilisateur est connecté
            if (! auth()->check()) {
                return $this->generateErrorImage();
            }

            // Décoder l'URL pour gérer les caractères spéciaux
            $decodedSku = urldecode($sku);

            // Nettoyer le SKU (au cas où il contiendrait encore des caractères problématiques)
            $cleanSku = strtoupper(trim($decodedSku));
            $cleanSku = preg_replace('/[^A-Z0-9\-_]/', '', $cleanSku);

            // Essayer de trouver le produit avec différentes variations du SKU
            $product = \App\Models\Product::where('company_id', auth()->user()->company_id)
                ->where(function ($query) use ($sku, $decodedSku, $cleanSku) {
                    $query->where('sku', $sku)
                        ->orWhere('sku', $decodedSku)
                        ->orWhere('sku', $cleanSku);
                })
                ->first();

            if (! $product) {
                return $this->generateErrorImage();
            }

            // Utiliser le SKU de la base de données pour générer le code-barres
            $finalSku = $product->sku;

            // Configure le générateur de code-barres avec le nouveau package
            $generator = new BarcodeGeneratorSVG;

            // Génère le code-barres au format SVG avec le SKU final
            $barcode = $generator->getBarcode($finalSku, $generator::TYPE_CODE_128, 2, 60);

            // Retourne l'image SVG avec les bons en-têtes HTTP
            return response($barcode, 200, [
                'Content-Type' => 'image/svg+xml',
                'Cache-Control' => 'public, max-age=3600', // Cache pour 1 heure
                'Content-Length' => strlen($barcode),
            ]);
        } catch (\Exception $e) {
            // En cas d'erreur, retourner une image d'erreur simple
            return $this->generateErrorImage();
        }
    }

    /**
     * Génère une image d'erreur simple
     */
    private function generateErrorImage()
    {
        // Retourner un SVG d'erreur simple et fiable
        $svgError = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="60" viewBox="0 0 200 60">
            <rect width="200" height="60" fill="white" stroke="red" stroke-width="1"/>
            <text x="100" y="35" font-family="Arial, sans-serif" font-size="12" text-anchor="middle" fill="red">Erreur Code-barres</text>
        </svg>';

        return response($svgError, 200, ['Content-Type' => 'image/svg+xml']);
    }
}
