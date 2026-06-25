<?php

/*
|--------------------------------------------------------------------------
| WondoStock Web Routes
|--------------------------------------------------------------------------
|
| Organisation des routes :
| 1. Routes communes (langue, auth)
| 2. Routes SaaS (multi-tenant avec isolation)
| 3. Routes Admin Global (gestion plateforme)
|
*/

use App\Http\Controllers\Auth\LogoutController;
// ============================================================================
// CONTROLLERS - Communs
// ============================================================================
use App\Http\Controllers\Deliveries\DeliveryPdfController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProductBarcodeController;
use App\Http\Controllers\Settings\CountryBranchController;
// ============================================================================
// LIVEWIRE - Auth & Communs
// ============================================================================
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
// ============================================================================
// LIVEWIRE - Dashboard SaaS (Multi-tenant)
// ============================================================================
use App\Livewire\Customers\Index as CustomerIndex;
use App\Livewire\Dashboard;
use App\Livewire\Deliveries\Index as DeliveryIndex;
use App\Livewire\Deliveries\Settings as DeliverySettings;
// SaaS - Produits
use App\Livewire\Deliveries\TripForm;
use App\Livewire\Deliveries\TripShow;
use App\Livewire\Documents\CreditNoteForm;
use App\Livewire\Documents\DocumentForm;
use App\Livewire\Documents\Index as DocumentIndex;
use App\Livewire\Documents\Show as DocumentShow;
use App\Livewire\Products\Index as ProductIndex;
use App\Livewire\Products\PrintLabels as ProductLabels;
use App\Livewire\Products\ProductForm;
use App\Livewire\Products\ProductSettings;
use App\Livewire\Profile\Index as ProfileIndex;
// SaaS - Documents & Ventes
use App\Livewire\Purchases\Index as PurchasesIndex;
use App\Livewire\Purchases\PurchaseOrderForm;
use App\Livewire\Purchases\Show as PurchasesShow;
use App\Livewire\Reports\Index as ReportsIndex;
// SaaS - Gestion Relations
use App\Livewire\Settings\Company\Index as CompanySettingsIndex;
use App\Livewire\Settings\Invitations\Index as InvitationsIndex;
// SaaS - Stock
use App\Livewire\Settings\Numbering as NumberingSettings;
use App\Livewire\Settings\Roles\Index as RolesIndex;
use App\Livewire\Settings\Subscription\Index as SubscriptionIndex;
// SaaS - Achats
use App\Livewire\Settings\Users\Index as UsersIndex;
use App\Livewire\Stock\Movements\Index as StockMovementsIndex;
use App\Livewire\Stock\StockEntry;
// SaaS - Magasins & Rapports
use App\Livewire\Stock\TransferForm;
use App\Livewire\StockPurchases\Index as StockPurchaseIndex;
// SaaS - Paramètres
use App\Livewire\StockPurchases\TripForm as StockPurchaseForm;
use App\Livewire\StockPurchases\TripShow as StockPurchaseShow;
use App\Livewire\StoreActivity\Dashboard as StoreActivityDashboard;
use App\Livewire\Stores\Index as StoreIndex;
use App\Livewire\Suppliers\Index as SuppliersIndex;
use Illuminate\Support\Facades\Route;

// ============================================================================
// ROUTES COMMUNES (Langue, Auth, Invitations)
// ============================================================================

// Changement de langue (accessible à tous)
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

// Routes pour les utilisateurs non connectés
Route::middleware('guest')->group(function () {
    Route::get('/', Login::class)->name('home');
    Route::post('/', function () {
        return redirect()->route('login');
    })->name('home.post'); // Handle POST to root
    Route::get('/register', Register::class)->name('register');
    Route::get('/login', Login::class)->name('login');

    // Routes d'invitation (accessible sans connexion)
    Route::get('/invitation/{token}', [InvitationController::class, 'show'])->name('invitation.show');
    Route::post('/invitation/{token}/accept', [InvitationController::class, 'accept'])->name('invitation.accept');
});

// ============================================================================
// ROUTES SAAS (Multi-tenant avec isolation par entreprise)
// ============================================================================

Route::middleware(['auth'])->group(function () {
    // Déconnexion
    Route::post('/logout', LogoutController::class)->name('logout');

    // ----------------------------------------
    // DASHBOARD & ACTIVITÉ
    // ----------------------------------------
    Route::get('/dashboard', Dashboard::class)->name('dashboard')->middleware('check_admin_redirect');
    Route::get('/store-activity/{storeId?}', StoreActivityDashboard::class)->name('store-activity.dashboard');
    Route::get('/profile', ProfileIndex::class)->name('profile.index');

    // ----------------------------------------
    // PRODUITS & INVENTAIRE
    // ----------------------------------------
    Route::get('/product-settings', ProductSettings::class)->name('products.settings');
    Route::get('/products', ProductIndex::class)->name('products.index');
    Route::get('/products/{product}/edit', ProductForm::class)->name('products.edit')->where('product', '[0-9]+');
    Route::get('/products/advanced-features', ProductForm::class)->name('products.advanced')->middleware('feature_lock:variable_products');
    Route::get('/products/print-labels', ProductLabels::class)->name('products.print-labels');
    Route::get('/products/{sku}/barcode', ProductBarcodeController::class)->name('products.barcode');

    // ----------------------------------------
    // GESTION STOCK
    // ----------------------------------------
    Route::get('/stock-entry', StockEntry::class)->name('stock.entry');
    Route::get('/stock/movements', StockMovementsIndex::class)->name('stock.movements.index');
    Route::get('/stock/transfer', TransferForm::class)->name('stock.transfer');

    // ----------------------------------------
    // RELATIONS (Clients/Fournisseurs)
    // ----------------------------------------
    Route::get('/customers', CustomerIndex::class)->name('customers.index');
    Route::get('/suppliers', SuppliersIndex::class)->name('suppliers.index');

    // ----------------------------------------
    // ACHATS
    // ----------------------------------------
    Route::get('/purchases', PurchasesIndex::class)->name('purchases.index');
    Route::get('/purchases/create', PurchaseOrderForm::class)->name('purchases.create');
    Route::get('/purchases/{document}/edit', PurchaseOrderForm::class)->name('purchases.edit');
    Route::get('/purchases/{document}', PurchasesShow::class)->name('purchases.show');

    // ----------------------------------------
    // VENTES & DOCUMENTS
    // ----------------------------------------
    Route::get('/documents/{invoice}/credit-note/create', CreditNoteForm::class)->name('documents.credit-note.create');
    Route::get('/documents', DocumentIndex::class)->name('documents.index');
    Route::get('/documents/create', DocumentForm::class)->name('documents.create');
    Route::get('/documents/{document}', DocumentShow::class)->name('documents.show');
    Route::get('/documents/{document}/edit', DocumentForm::class)->name('documents.edit');

    // ----------------------------------------
    // MAGASINS & SUCCURSALES
    // ----------------------------------------
    Route::get('/stores', StoreIndex::class)->name('stores.index');
    Route::get('/country-branch/create', [CountryBranchController::class, 'create'])->name('country-branch.create');
    Route::post('/country-branch', [CountryBranchController::class, 'store'])->name('country-branch.store');
    Route::get('/country-branch/{store}/edit', [CountryBranchController::class, 'edit'])->name('country-branch.edit');
    Route::put('/country-branch/{store}', [CountryBranchController::class, 'update'])->name('country-branch.update');

    // ----------------------------------------
    // DÉPENSES (module optionnel)
    // ----------------------------------------
    Route::middleware('module:expenses')->prefix('expenses')->name('expenses.')->group(function () {
        Route::get('/', \App\Livewire\Expenses\Index::class)->name('index');
        Route::get('/settings', \App\Livewire\Expenses\Settings::class)->name('settings');
    });

    // ----------------------------------------
    // SALAIRES (module optionnel)
    // ----------------------------------------
    Route::middleware('module:salaries')->prefix('salaries')->name('salaries.')->group(function () {
        Route::get('/', \App\Livewire\Salaries\Index::class)->name('index');
        Route::get('/advances', \App\Livewire\Salaries\Advances::class)->name('advances.index');
        Route::get('/slips/{slip}/pdf', [\App\Http\Controllers\Salaries\SalaryPdfController::class, 'download'])->name('slip.pdf');
        Route::get('/slips/{slip}', \App\Livewire\Salaries\SlipShow::class)->name('slip.show');
        Route::get('/{period}', \App\Livewire\Salaries\PeriodShow::class)->name('period.show');
    });

    // ----------------------------------------
    // EMPLOYÉS (module salaries)
    // ----------------------------------------
    Route::middleware('module:salaries')->prefix('employees')->name('employees.')->group(function () {
        Route::get('/', \App\Livewire\Employees\Index::class)->name('index');
    });

    // ----------------------------------------
    // DASHBOARD FINANCIER (module advanced_reports)
    // ----------------------------------------
    Route::middleware('module:advanced_reports')->group(function () {
        Route::get('/finance', \App\Livewire\Finance\Dashboard::class)->name('finance.dashboard');
    });

    // ----------------------------------------
    // MODULE CAISSE
    // ----------------------------------------
    Route::middleware('module:caisse')->prefix('caisse')->name('caisse.')->group(function () {
        Route::get('/', \App\Livewire\Caisse\Index::class)->name('index');
        Route::get('/{session}', \App\Livewire\Caisse\SessionShow::class)->name('session.show');
    });

    // ----------------------------------------
    // VERSEMENTS DG (module cash_remittances)
    // ----------------------------------------
    Route::middleware('module:cash_remittances')->group(function () {
        Route::get('/finance/remittances', \App\Livewire\Finance\Remittances::class)->name('finance.remittances');
    });

    // ----------------------------------------
    // POINT DE COMPTABILITÉ (module monthly_accounting)
    // ----------------------------------------
    Route::middleware('module:monthly_accounting')->group(function () {
        Route::get('/finance/monthly-accounting', \App\Livewire\Finance\MonthlyAccounting::class)->name('finance.monthly-accounting');
        Route::get('/finance/monthly-accounting/pdf', [\App\Http\Controllers\Finance\MonthlyAccountingPdfController::class, 'download'])->name('finance.monthly-accounting.pdf');
    });

    // ----------------------------------------
    // LIVRAISONS (module optionnel)
    // ----------------------------------------
    Route::middleware('module:deliveries')->prefix('deliveries')->name('deliveries.')->group(function () {
        Route::get('/', DeliveryIndex::class)->name('index');
        Route::get('/create', TripForm::class)->name('create');
        Route::get('/settings', DeliverySettings::class)->name('settings');
        Route::get('/{trip}/edit', TripForm::class)->name('edit');
        Route::get('/{trip}/pdf/report', [DeliveryPdfController::class, 'report'])->name('pdf.report');
        Route::get('/{trip}/pdf/invoice', [DeliveryPdfController::class, 'invoice'])->name('pdf.invoice');
        Route::get('/{trip}', TripShow::class)->name('show');
    });

    // ----------------------------------------
    // ACHATS DE STOCK (sous-fonctionnalité des livraisons)
    // ----------------------------------------
    Route::middleware('module:deliveries')->prefix('stock-purchases')->name('stock-purchases.')->group(function () {
        Route::get('/', StockPurchaseIndex::class)->name('index');
        Route::get('/create', StockPurchaseForm::class)->name('create');
        Route::get('/{trip}/edit', StockPurchaseForm::class)->name('edit');
        Route::get('/{trip}', StockPurchaseShow::class)->name('show');
    });

    // ----------------------------------------
    // RAPPORTS & ANALYSES
    // ----------------------------------------
    Route::get('/reports', ReportsIndex::class)->name('reports.index');

    // ----------------------------------------
    // PARAMÈTRES ENTREPRISE
    // ----------------------------------------
    Route::get('/settings/company', CompanySettingsIndex::class)->name('settings.company.index');
    Route::get('/settings/users', UsersIndex::class)->name('settings.users.index');
    Route::get('/settings/roles', RolesIndex::class)->name('settings.roles.index')->middleware('can:feature-roles-permissions');
    Route::get('/settings/invitations', InvitationsIndex::class)->name('settings.invitations.index');
    Route::get('/settings/numbering', NumberingSettings::class)->name('settings.numbering');
    Route::get('/settings/subscription', SubscriptionIndex::class)->name('settings.subscription.index');
    Route::get('/settings/audit-log', \App\Livewire\Settings\AuditLog::class)->name('settings.audit-log');
});

// ============================================================================
// ROUTES ADMIN GLOBAL SUPPRIMÉES - Remplacées par Filament Admin Panel (/admin)
// ============================================================================
// L'ancien back-office admin Laravel a été remplacé par Filament v4
// Toute l'administration se fait maintenant via le panel Filament : /admin
