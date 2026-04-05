<?php

namespace App\Exceptions;

use Exception;

class FeatureBlockedException extends Exception
{
    protected string $featureKey;

    public function __construct(string $featureKey, string $reason = 'Fonctionnalité temporairement indisponible')
    {
        $this->featureKey = $featureKey;

        parent::__construct($reason, 403);
    }

    public function getFeatureKey(): string
    {
        return $this->featureKey;
    }

    public function render()
    {
        return response()->view('errors.feature-blocked', [
            'feature' => $this->featureKey,
            'message' => $this->message,
        ], 403);
    }
}
