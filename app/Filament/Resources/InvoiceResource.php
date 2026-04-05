<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
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

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-document-text';
    }

    public static function getNavigationLabel(): string
    {
        return 'Factures';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Gestion SaaS';
    }

    public static function getNavigationSort(): ?int
    {
        return 45;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('status', ['sent', 'overdue'])->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $overdueCount = static::getModel()::where('status', 'overdue')->count();

        if ($overdueCount > 0) {
            return 'danger';
        }

        $sentCount = static::getModel()::where('status', 'sent')->count();

        return $sentCount > 0 ? 'warning' : 'success';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Informations de la Facture
                TextInput::make('invoice_number')
                    ->label('Numéro de Facture')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(fn () => Invoice::generateInvoiceNumber())
                    ->helperText('Numéro unique généré automatiquement'),

                Select::make('company_id')
                    ->label('Entreprise')
                    ->relationship('company', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Select::make('subscription_id')
                    ->label('Abonnement')
                    ->relationship('subscription', 'id', function ($query, $get) {
                        if ($companyId = $get('company_id')) {
                            return $query->where('company_id', $companyId);
                        }

                        return $query;
                    })
                    ->searchable()
                    ->preload()
                    ->helperText('Abonnement lié à cette facture (optionnel)'),

                Select::make('status')
                    ->label('Statut')
                    ->options([
                        'draft' => 'Brouillon',
                        'sent' => 'Envoyée',
                        'paid' => 'Payée',
                        'overdue' => 'En retard',
                        'cancelled' => 'Annulée',
                    ])
                    ->default('draft')
                    ->required(),

                // Dates
                DatePicker::make('issue_date')
                    ->label('Date d\'Émission')
                    ->required()
                    ->default(now()),

                DatePicker::make('due_date')
                    ->label('Date d\'Échéance')
                    ->required()
                    ->default(now()->addDays(30))
                    ->after('issue_date'),

                DatePicker::make('paid_at')
                    ->label('Date de Paiement')
                    ->visible(fn ($get) => $get('status') === 'paid'),

                // Montants
                TextInput::make('amount')
                    ->label('Montant HT (FCFA)')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $taxAmount = $get('tax_amount') ?: 0;
                        $set('total_amount', ($state ?: 0) + $taxAmount);
                    }),

                TextInput::make('tax_amount')
                    ->label('Montant Taxe (FCFA)')
                    ->numeric()
                    ->default(0)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $amount = $get('amount') ?: 0;
                        $set('total_amount', $amount + ($state ?: 0));
                    }),

                TextInput::make('total_amount')
                    ->label('Montant Total (FCFA)')
                    ->required()
                    ->numeric()
                    ->readonly()
                    ->dehydrated(),

                // Adresse de Facturation
                Textarea::make('billing_address')
                    ->label('Adresse de Facturation')
                    ->maxLength(1000)
                    ->placeholder('Adresse complète de facturation')
                    ->columnSpanFull(),

                // Notes
                Textarea::make('notes')
                    ->label('Notes Internes')
                    ->maxLength(1000)
                    ->placeholder('Notes pour usage interne...')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Numéro')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->copyable(),

                TextColumn::make('company.name')
                    ->label('Entreprise')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'secondary' => 'draft',
                        'warning' => 'sent',
                        'success' => 'paid',
                        'danger' => fn ($state) => in_array($state, ['overdue', 'cancelled']),
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'draft' => 'Brouillon',
                        'sent' => 'Envoyée',
                        'paid' => 'Payée',
                        'overdue' => 'En retard',
                        'cancelled' => 'Annulée',
                        default => $state
                    }),

                TextColumn::make('total_amount')
                    ->label('Montant Total')
                    ->money('XOF', divideBy: 100)
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('issue_date')
                    ->label('Émission')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('due_date')
                    ->label('Échéance')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => $record->isOverdue() ? 'danger' : null),

                TextColumn::make('paid_at')
                    ->label('Payée le')
                    ->date('d/m/Y')
                    ->placeholder('Non payée'),

                TextColumn::make('created_at')
                    ->label('Créée le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'draft' => 'Brouillon',
                        'sent' => 'Envoyée',
                        'paid' => 'Payée',
                        'overdue' => 'En retard',
                        'cancelled' => 'Annulée',
                    ]),

                Filter::make('overdue')
                    ->label('En retard')
                    ->query(fn (Builder $query) => $query->where('status', '!=', 'paid')
                        ->where('due_date', '<', now()))
                    ->toggle(),

                Filter::make('this_month')
                    ->label('Ce mois')
                    ->query(fn (Builder $query) => $query->whereBetween('created_at', [
                        now()->startOfMonth(),
                        now()->endOfMonth(),
                    ]))
                    ->toggle(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('mark_sent')
                        ->label('Marquer comme envoyées')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('info')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'sent'])),

                    BulkAction::make('mark_paid')
                        ->label('Marquer comme payées')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update([
                            'status' => 'paid',
                            'paid_at' => now(),
                        ])),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['company', 'subscription']);
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view' => Pages\ViewInvoice::route('/{record}'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
