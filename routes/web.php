<?php

use App\Livewire\Dashboard;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Stock\StockEntry;
use App\Livewire\Stock\TransferForm;
use Illuminate\Support\Facades\Route;
use App\Livewire\Products\ProductForm;
use App\Livewire\Documents\DocumentForm;
use App\Livewire\Documents\CreditNoteForm;
use App\Livewire\Products\ProductSettings;
use App\Livewire\Stores\Index as StoreIndex;
use App\Livewire\Purchases\PurchaseOrderForm;
use App\Http\Controllers\Auth\LogoutController;
use App\Livewire\Profile\Index as ProfileIndex;
use App\Livewire\Reports\Index as ReportsIndex;
use App\Livewire\Documents\Show as DocumentShow;
use App\Livewire\Products\Index as ProductIndex;
use App\Livewire\Purchases\Show as PurchasesShow;
use App\Http\Controllers\ProductBarcodeController;
use App\Livewire\Customers\Index as CustomerIndex;
use App\Livewire\Documents\Index as DocumentIndex;
use App\Livewire\Purchases\Index as PurchasesIndex;
use App\Livewire\Suppliers\Index as SuppliersIndex;
use App\Livewire\Settings\Users\Index as UsersIndex;
use App\Livewire\Stock\Movements\Index as StockMovementsIndex;
use App\Livewire\Settings\Company\Index as CompanySettingsIndex;
use App\Livewire\Products\PrintLabels as ProductLabels;
use App\Livewire\Settings\Numbering as NumberingSettings;
use App\Livewire\Settings\Roles\Index as RolesIndex;
use App\Livewire\Settings\Subscription\Index as SubscriptionIndex;
use App\Http\Controllers\Admin\GlobalAdminController;

Route::middleware('guest')->group(function () {
    Route::get('/', Login::class)->name('home');
    Route::get('/register', Register::class)->name('register');
    Route::get('/login', Login::class)->name('login');
});




Route::middleware(['auth'])->group(function () {
    Route::post('/logout', LogoutController::class)->name('logout');
    // La route /dashboard pointe maintenant vers notre nouveau composant
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/profile', ProfileIndex::class)->name('profile.index');
    Route::get('/settings/subscription', SubscriptionIndex::class)->name('settings.subscription.index');
    Route::get('/settings/roles', RolesIndex::class)->name('settings.roles.index')->middleware('can:feature-roles-permissions');

    Route::get('/stores', StoreIndex::class)->name('stores.index');

    Route::get('/product-settings', ProductSettings::class)->name('products.settings');
    Route::get('/products', ProductIndex::class)->name('products.index');
    // On ajoute une contrainte where pour que {product} ne corresponde qu'à des nombres.
    Route::get('/products/{product}/edit', ProductForm::class)->name('products.edit')->where('product', '[0-9]+');

    Route::get('/products/print-labels', ProductLabels::class)->name('products.print-labels');
    Route::get('/products/{sku}/barcode', ProductBarcodeController::class)->name('products.barcode');

    Route::get('/stock-entry', StockEntry::class)->name('stock.entry');
    Route::get('/stock/movements', StockMovementsIndex::class)->name('stock.movements.index');
    Route::get('/stock/transfer', TransferForm::class)->name('stock.transfer');

    Route::get('/customers', CustomerIndex::class)->name('customers.index');
    Route::get('/suppliers', SuppliersIndex::class)->name('suppliers.index');

    Route::get('/purchases', PurchasesIndex::class)->name('purchases.index');
    Route::get('/purchases/create', PurchaseOrderForm::class)->name('purchases.create');
    Route::get('/purchases/{document}/edit', PurchaseOrderForm::class)->name('purchases.edit');
    Route::get('/purchases/{document}', PurchasesShow::class)->name('purchases.show');

    // On passe l'invoice source à la route
    Route::get('/documents/{invoice}/credit-note/create', CreditNoteForm::class)->name('documents.credit-note.create');
    Route::get('/documents', DocumentIndex::class)->name('documents.index');
    Route::get('/documents/create', DocumentForm::class)->name('documents.create');
    Route::get('/documents/{document}', DocumentShow::class)->name('documents.show');
    Route::get('/documents/{document}/edit', DocumentForm::class)->name('documents.edit');

    Route::get('/settings/users', UsersIndex::class)->name('settings.users.index');
    Route::get('/settings/company', CompanySettingsIndex::class)->name('settings.company.index');
    Route::get('/reports', ReportsIndex::class)->name('reports.index');
    Route::get('/settings/numbering', NumberingSettings::class)->name('settings.numbering');
});

// Routes pour l'administration globale
Route::prefix('global-admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [GlobalAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/companies', [GlobalAdminController::class, 'companies'])->name('companies.index');
    Route::get('/companies/{company}', [GlobalAdminController::class, 'showCompany'])->name('companies.show');
    Route::patch('/companies/{company}/toggle-status', [GlobalAdminController::class, 'toggleCompanyStatus'])->name('companies.toggle-status');
    Route::get('/system-stats', [GlobalAdminController::class, 'systemStats'])->name('system-stats');
});
