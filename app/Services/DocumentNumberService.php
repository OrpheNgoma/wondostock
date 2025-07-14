<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Setting;

class DocumentNumberService
{
    public static function generate(int $companyId, string $type): string
    {
        // Clés pour les paramètres
        $prefixKey = "{$type}_prefix";
        $counterKey = "{$type}_last_number";
        $formatKey = "{$type}_format";

        // Récupérer les paramètres ou utiliser des valeurs par défaut
        $prefix = Setting::where('company_id', $companyId)
            ->where('key', $prefixKey)
            ->value('value') ?? strtoupper(substr($type, 0, 4)) . '-';
            
        $lastNumber = (int) (Setting::where('company_id', $companyId)
            ->where('key', $counterKey)
            ->value('value') ?? 0);
            
        $format = Setting::where('company_id', $companyId)
            ->where('key', $formatKey)
            ->value('value') ?? '{PRE}-{ANNEE}-{NUMERO}';

        $nextNumber = $lastNumber + 1;

        // Mettre à jour le compteur dans la base de données
        Setting::updateOrCreate(
            ['company_id' => $companyId, 'key' => $counterKey],
            ['value' => $nextNumber]
        );

        // Formater le numéro avec des zéros au début (ex: 00001)
        $formattedNumber = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        // Remplacer les placeholders dans le format
        $documentNumber = str_replace(
            ['{PRE}', '{ANNEE}', '{NUMERO}'],
            [$prefix, date('Y'), $formattedNumber],
            $format
        );

        return $documentNumber;
    }
}