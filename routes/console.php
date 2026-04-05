<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Configuration des tâches automatisées pour la facturation SaaS
 */
Schedule::command('billing:generate-monthly')
    ->monthlyOn(1, '06:00') // Le 1er de chaque mois à 6h
    ->timezone('Africa/Abidjan')
    ->withoutOverlapping(60) // Éviter les chevauchements avec timeout de 60 min
    ->runInBackground()
    ->emailOutputOnFailure('admin@wondostock.com')
    ->appendOutputTo(storage_path('logs/billing-monthly.log'));

Schedule::command('billing:send-reminders')
    ->dailyAt('08:00') // Tous les jours à 8h
    ->timezone('Africa/Abidjan')
    ->withoutOverlapping(30) // Timeout de 30 min
    ->runInBackground()
    ->emailOutputOnFailure('admin@wondostock.com')
    ->appendOutputTo(storage_path('logs/payment-reminders.log'));

Schedule::command('billing:send-reminders --days=15 --suspend-after=45')
    ->weeklyOn(1, '09:00') // Tous les lundis à 9h pour les cas critiques
    ->timezone('Africa/Abidjan')
    ->withoutOverlapping(30)
    ->runInBackground()
    ->emailOutputOnFailure('admin@wondostock.com')
    ->appendOutputTo(storage_path('logs/payment-reminders-weekly.log'));

// Commande pour nettoyer les anciens logs
Schedule::call(function () {
    $files = [
        storage_path('logs/billing-monthly.log'),
        storage_path('logs/payment-reminders.log'),
        storage_path('logs/payment-reminders-weekly.log'),
    ];

    foreach ($files as $file) {
        if (file_exists($file) && filesize($file) > 10 * 1024 * 1024) { // 10MB
            file_put_contents($file, ''); // Vider le fichier
        }
    }
})->monthly();
