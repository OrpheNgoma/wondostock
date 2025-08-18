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
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProductBarcodeController;
use App\Http\Controllers\Settings\CountryBranchController;
use App\Livewire\Auth\Login;
// ============================================================================
// LIVEWIRE - Auth & Communs
// ============================================================================
use App\Livewire\Auth\Register;
use App\Livewire\Customers\Index as CustomerIndex;
// ============================================================================
// LIVEWIRE - Dashboard SaaS (Multi-tenant)
// ============================================================================
use App\Livewire\Dashboard;
use App\Livewire\Documents\CreditNoteForm;
use App\Livewire\Documents\DocumentForm;
// SaaS - Produits
use App\Livewire\Documents\Index as DocumentIndex;
use App\Livewire\Documents\Show as DocumentShow;
use App\Livewire\Products\Index as ProductIndex;
use App\Livewire\Products\PrintLabels as ProductLabels;
// SaaS - Documents & Ventes
use App\Livewire\Products\ProductForm;
use App\Livewire\Products\ProductSettings;
use App\Livewire\Profile\Index as ProfileIndex;
use App\Livewire\Purchases\Index as PurchasesIndex;
// SaaS - Gestion Relations
use App\Livewire\Purchases\PurchaseOrderForm;
use App\Livewire\Purchases\Show as PurchasesShow;
// SaaS - Stock
use App\Livewire\Reports\Index as ReportsIndex;
use App\Livewire\Settings\Company\Index as CompanySettingsIndex;
use App\Livewire\Settings\Invitations\Index as InvitationsIndex;
// SaaS - Achats
use App\Livewire\Settings\Numbering as NumberingSettings;
use App\Livewire\Settings\Roles\Index as RolesIndex;
use App\Livewire\Settings\Subscription\Index as SubscriptionIndex;
// SaaS - Magasins & Rapports
use App\Livewire\Settings\Users\Index as UsersIndex;
use App\Livewire\Stock\Movements\Index as StockMovementsIndex;
// SaaS - Paramètres
use App\Livewire\Stock\StockEntry;
use App\Livewire\Stock\TransferForm;
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
});

// ============================================================================
// ROUTES ADMIN GLOBAL (Gestion plateforme SaaS - Sans tenant isolation)
// ============================================================================

Route::prefix('admin')->name('admin.')->middleware(['auth', 'global_admin'])->group(function () {
    // Dashboard de l'administration globale
    Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard');

    // Gestion des entreprises clientes
    Route::get('/companies', \App\Livewire\Admin\CompaniesIndex::class)->name('companies.index');
    Route::get('/companies/create', \App\Livewire\Admin\CompanyCreate::class)->name('companies.create');
    Route::get('/companies/{company}', \App\Livewire\Admin\CompanyShow::class)->name('companies.show');
    Route::get('/companies/{company}/edit', \App\Livewire\Admin\CompanyEdit::class)->name('companies.edit');

    // Gestion des plans tarifaires
    Route::get('/plans', \App\Livewire\Admin\PlansIndex::class)->name('plans.index');
    Route::get('/plans/create', \App\Livewire\Admin\PlanCreate::class)->name('plans.create');
    Route::get('/plans/{plan}/edit', \App\Livewire\Admin\PlanEdit::class)->name('plans.edit');

    // Gestion des abonnements
    Route::get('/subscriptions', \App\Livewire\Admin\SubscriptionsIndex::class)->name('subscriptions.index');
    Route::get('/subscriptions/create', \App\Livewire\Admin\SubscriptionCreate::class)->name('subscriptions.create');
    Route::get('/subscriptions/{subscription}/edit', \App\Livewire\Admin\SubscriptionEdit::class)->name('subscriptions.edit');

    // Gestion de la facturation et paiements
    Route::get('/invoices', \App\Livewire\Admin\InvoicesIndex::class)->name('invoices.index');
    Route::get('/invoices/{invoice}', \App\Livewire\Admin\InvoiceShow::class)->name('invoices.show');
    Route::get('/invoices/{invoice}/edit', \App\Livewire\Admin\InvoiceEdit::class)->name('invoices.edit');

    // Gestion des notifications de paiement
    Route::get('/payment-notifications', \App\Livewire\GlobalAdmin\PaymentNotifications::class)->name('payment-notifications.index');

    // PDF des factures
    Route::get('/invoices/{invoice}/pdf', function (\App\Models\Invoice $invoice, \App\Services\InvoicePdfService $pdfService) {
        return $pdfService->streamPdf($invoice);
    })->name('invoices.pdf');
});
