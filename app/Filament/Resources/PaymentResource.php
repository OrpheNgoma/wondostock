<?php

namespace App\Filament\Resources;

use App\Enums\PaymentMethod;
use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-banknotes';
    }

    public static function getNavigationLabel(): string
    {
        return 'Paiements';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Gestion SaaS';
    }

    public static function getNavigationSort(): ?int
    {
        return 50;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'completed')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $completedCount = static::getModel()::where('status', 'completed')->count();
        $totalCount = static::getModel()::count();

        if ($totalCount === 0) {
            return 'gray';
        }

        $percentage = ($completedCount / $totalCount) * 100;

        if ($percentage >= 90) {
            return 'success';
        } elseif ($percentage >= 70) {
            return 'warning';
        } else {
            return 'danger';
        }
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Informations du Paiement
                Select::make('invoice_id')
                    ->label('Facture')
                    ->relationship('invoice', 'invoice_number')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->helperText('Sélectionner la facture concernée par le paiement')
                    ->columnSpan(['lg' => 6]),

                Select::make('company_id')
                    ->label('Entreprise')
                    ->relationship('company', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->helperText('Entreprise effectuant le paiement')
                    ->columnSpan(['lg' => 6]),

                TextInput::make('amount')
                    ->label('Montant (FCFA)')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->helperText('Montant en centimes FCFA (ex: 1000 = 10 FCFA)')
                    ->columnSpan(['lg' => 6]),

                Select::make('payment_method')
                    ->label('Méthode de Paiement')
                    ->options(PaymentMethod::getOptions())
                    ->default('card')
                    ->required()
                    ->helperText('Mode de paiement utilisé')
                    ->columnSpan(['lg' => 6]),

                // Statut et Traçabilité
                Select::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En Attente',
                        'processing' => 'En Cours',
                        'completed' => 'Terminé',
                        'failed' => 'Échec',
                        'cancelled' => 'Annulé',
                        'refunded' => 'Remboursé',
                    ])
                    ->default('pending')
                    ->required()
                    ->helperText('État actuel du paiement')
                    ->columnSpan(['lg' => 6]),

                TextInput::make('transaction_id')
                    ->label('ID de Transaction')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->placeholder('Ex: TXN_20250119_001')
                    ->helperText('Identifiant unique de la transaction (généré automatiquement)')
                    ->columnSpan(['lg' => 6]),

                // Dates
                DateTimePicker::make('paid_at')
                    ->label('Date de Paiement')
                    ->default(now())
                    ->helperText('Date et heure du paiement effectif')
                    ->columnSpan(['lg' => 6]),

                DateTimePicker::make('processed_at')
                    ->label('Date de Traitement')
                    ->helperText('Date de traitement par le système de paiement')
                    ->columnSpan(['lg' => 6]),

                // Notes et Métadonnées
                Textarea::make('notes')
                    ->label('Notes')
                    ->maxLength(1000)
                    ->placeholder('Notes et commentaires sur le paiement...')
                    ->columnSpan('full'),

                Textarea::make('gateway_response')
                    ->label('Réponse Gateway')
                    ->maxLength(2000)
                    ->placeholder('Réponse brute du gateway de paiement (JSON)')
                    ->helperText('Stockage de la réponse complète du provider de paiement')
                    ->columnSpan('full'),
            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_id')
                    ->label('Transaction')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('medium'),

                TextColumn::make('invoice.invoice_number')
                    ->label('Facture')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => $record->invoice ? route('filament.admin.resources.invoices.view', $record->invoice) : null)
                    ->color('primary'),

                TextColumn::make('company.name')
                    ->label('Entreprise')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('amount')
                    ->label('Montant')
                    ->money('XOF', divideBy: 100)
                    ->sortable()
                    ->alignEnd(),

                BadgeColumn::make('payment_method')
                    ->label('Méthode')
                    ->colors([
                        'info' => 'card',
                        'success' => 'bank_transfer',
                        'warning' => 'mobile_money',
                        'secondary' => fn ($state) => in_array($state, ['cash', 'check', 'other']),
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'card' => 'Carte',
                        'bank_transfer' => 'Virement',
                        'mobile_money' => 'Mobile Money',
                        'cash' => 'Espèces',
                        'check' => 'Chèque',
                        'other' => 'Autre',
                        default => $state
                    }),

                BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'success' => 'completed',
                        'warning' => fn ($state) => in_array($state, ['pending', 'processing']),
                        'danger' => fn ($state) => in_array($state, ['failed', 'cancelled']),
                        'info' => 'refunded',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'En Attente',
                        'processing' => 'En Cours',
                        'completed' => 'Terminé',
                        'failed' => 'Échec',
                        'cancelled' => 'Annulé',
                        'refunded' => 'Remboursé',
                        default => $state
                    }),

                TextColumn::make('paid_at')
                    ->label('Date Paiement')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En Attente',
                        'processing' => 'En Cours',
                        'completed' => 'Terminé',
                        'failed' => 'Échec',
                        'cancelled' => 'Annulé',
                        'refunded' => 'Remboursé',
                    ]),

                SelectFilter::make('payment_method')
                    ->label('Méthode de Paiement')
                    ->options(PaymentMethod::getOptions()),

                Filter::make('completed_today')
                    ->label('Terminés aujourd\'hui')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'completed')
                        ->whereDate('paid_at', now()->toDateString())
                    ),

                Filter::make('large_payments')
                    ->label('Paiements > 50,000 FCFA')
                    ->query(fn (Builder $query): Builder => $query->where('amount', '>', 5000000) // 50,000 FCFA en centimes
                    ),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('mark_completed')
                        ->label('Marquer comme Terminé')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update([
                            'status' => 'completed',
                            'processed_at' => now(),
                        ])),

                    BulkAction::make('mark_failed')
                        ->label('Marquer comme Échec')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update([
                            'status' => 'failed',
                            'processed_at' => now(),
                        ])),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['invoice', 'company']);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'view' => Pages\ViewPayment::route('/{record}'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
