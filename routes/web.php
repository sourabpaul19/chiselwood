<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\EmployeeTypeController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ProjectTypeController;
use App\Http\Controllers\Admin\ProjectStatusController;
use App\Http\Controllers\Admin\VendorCategoryController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\LeadSourceController;
use App\Http\Controllers\Admin\LeadStatusController;
use App\Http\Controllers\Admin\TaskPriorityController;
use App\Http\Controllers\Admin\TaskStatusController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\InventoryCategoryController;
use App\Http\Controllers\Admin\InventoryItemController;
use App\Http\Controllers\Admin\InventoryUnitController;
use App\Http\Controllers\Admin\InventoryBrandController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CreditNoteController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Gstr1Controller;
use App\Http\Controllers\Admin\ClientLedgerController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\GoodsReceiptController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\ExpenseCategoryController;
use App\Http\Controllers\Admin\FinancialReportController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\InventoryVendorController;
use App\Http\Controllers\Admin\InventoryVendorCategoryController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
            ->name('admin.dashboard');
    });

    Route::middleware('role:client')->group(function () {
        Route::get('/client/dashboard', [ClientController::class, 'dashboard'])
            ->name('client.dashboard');
    });

});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
    Route::get('/clients/trash', [ClientController::class, 'trash'])->name('clients.trash');
    Route::post('/clients/{id}/restore', [ClientController::class, 'restore'])->name('clients.restore');
    Route::delete('/clients/{id}/force', [ClientController::class, 'force'])->name('clients.force');
    Route::patch('/clients/{client}/status-toggle', [ClientController::class, 'toggleStatus'])->name('clients.status.toggle');
    Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');

    Route::get('staff', [StaffController::class, 'index'])->name('staff.index');
    Route::get('staff/create', [StaffController::class, 'create'])->name('staff.create');

    /* 🔥 MOVE THIS UP */
    Route::get('staff/trash', [StaffController::class, 'trash'])->name('staff.trash');

    Route::post('staff', [StaffController::class, 'store'])->name('staff.store');
    Route::get('staff/{user}/edit', [StaffController::class, 'edit'])->name('staff.edit');
    Route::put('staff/{user}', [StaffController::class, 'update'])->name('staff.update');
    Route::delete('staff/{user}', [StaffController::class, 'destroy'])->name('staff.destroy');
    Route::post('staff/{id}/restore', [StaffController::class, 'restore'])->name('staff.restore');
    Route::delete('staff/{id}/force', [StaffController::class, 'force'])->name('staff.force');
    Route::post('staff/{user}/status-toggle', [StaffController::class, 'toggleStatus'])->name('staff.status.toggle');

    /* ⛔ KEEP THIS LAST */
    Route::get('staff/{user}', [StaffController::class, 'show'])->name('staff.show');
    
    /* ======================
    DEPARTMENTS
    ====================== */
    Route::resource('departments', DepartmentController::class)->except(['show']);
    Route::get('departments/trash', [DepartmentController::class,'trash'])->name('departments.trash');
    Route::get('departments/{id}/restore', [DepartmentController::class,'restore'])->name('departments.restore');
    Route::get('departments/{id}/force',[DepartmentController::class,'force'])->name('departments.force');
    Route::patch('departments/{department}/status-toggle',[DepartmentController::class, 'toggleStatus'])->name('departments.status.toggle');


    /* ======================
    EMPLOYEE TYPES
    ====================== */
    Route::resource('employee-types', EmployeeTypeController::class)->except(['show']);
    Route::get('employee-types/trash', [EmployeeTypeController::class,'trash'])->name('employee-types.trash');
    Route::get('employee-types/{id}/restore', [EmployeeTypeController::class,'restore'])->name('employee-types.restore');
    Route::get('employee-types/{id}/force',[EmployeeTypeController::class,'force'])->name('employee-types.force');
    Route::patch('employee-types/{employeeType}/status-toggle',[EmployeeTypeController::class, 'toggleStatus'])->name('employee-types.status.toggle');


    /* ======================
    PROJECTS
    ====================== */

    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create');

    /* 🔥 TRASH ROUTES MUST COME FIRST */
    Route::get('projects/trash', [ProjectController::class, 'trash'])->name('projects.trash');
    Route::post('projects/{id}/restore', [ProjectController::class, 'restore'])->name('projects.restore');
    Route::delete('projects/{id}/force', [ProjectController::class, 'force'])->name('projects.force');

    Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    /* ⛔ KEEP THIS ABSOLUTELY LAST */
    Route::get('projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::post('projects/{project}/status-toggle',[ProjectController::class, 'toggleStatus'])->name('projects.status.toggle');



    /* PROJECT TYPES */
    Route::resource('project-types', ProjectTypeController::class)->except(['show', 'create']);
    Route::get('project-types/trash', [ProjectTypeController::class, 'trash'])->name('project-types.trash');
    Route::post('project-types/{id}/restore', [ProjectTypeController::class, 'restore'])->name('project-types.restore');
    Route::delete('project-types/{id}/force', [ProjectTypeController::class, 'force'])->name('project-types.force');
    Route::patch('project-types/{types}/status-toggle',[ProjectTypeController::class, 'toggleStatus'])->name('project-types.status.toggle');

    /* PROJECT STATUSES */
    Route::resource('project-statuses', ProjectStatusController::class)->except(['show', 'create']);
    Route::get('project-statuses/trash', [ProjectStatusController::class, 'trash'])->name('project-statuses.trash');
    Route::post('project-statuses/{id}/restore', [ProjectStatusController::class, 'restore'])->name('project-statuses.restore');
    Route::delete('project-statuses/{id}/force', [ProjectStatusController::class, 'force'])->name('project-statuses.force');
    Route::patch('project-statuses/{statuses}/status-toggle',[ProjectStatusController::class, 'toggleStatus'])->name('project-statuses.status.toggle');

    /* VENDOR CATGORIES */
    Route::resource('vendor-category', VendorCategoryController::class)->except(['show', 'create']);
    Route::get('vendor-category/trash', [VendorCategoryController::class, 'trash'])->name('vendor-category.trash');
    Route::post('vendor-category/{id}/restore', [VendorCategoryController::class, 'restore'])->name('vendor-category.restore');
    Route::delete('vendor-category/{id}/force', [VendorCategoryController::class, 'force'])->name('vendor-category.force');
    Route::patch('vendor-category/{statuses}/status-toggle',[VendorCategoryController::class, 'toggleStatus'])->name('vendor-category.status.toggle');

    // Route::get('vendors', [VendorController::class, 'index'])->name('vendors.index');
    // Route::get('vendors/create', [VendorController::class, 'create'])->name('vendors.create');
    // Route::post('vendors/{vendor}/status', [VendorController::class, 'toggleStatus']);



    Route::get('vendors', [VendorController::class, 'index'])->name('vendors.index');
    Route::get('vendors/create', [VendorController::class, 'create'])->name('vendors.create');
    Route::post('vendors', [VendorController::class, 'store'])->name('vendors.store');
    Route::get('vendors/{user}/edit', [VendorController::class, 'edit'])->name('vendors.edit');
    Route::put('vendors/{user}', [VendorController::class, 'update'])->name('vendors.update');
    Route::post('vendors/{user}/status-toggle', [VendorController::class, 'toggleStatus'])->name('vendors.status.toggle');
    Route::delete('vendors/{user}', [VendorController::class, 'destroy'])->name('vendors.destroy');
    Route::get('vendors/trash', [VendorController::class, 'trash'])->name('vendors.trash');
    Route::post('vendors/{id}/restore', [VendorController::class, 'restore'])->name('vendors.restore');
    Route::get('vendors/{id}/force', [VendorController::class, 'force'])->name('vendors.force');
    Route::get('/vendors/{user}', [VendorController::class, 'show'])->name('vendors.show');

    /* VENDOR CATGORIES */
    Route::resource('inventory-vendor-category', InventoryVendorCategoryController::class)->except(['show', 'create']);
    Route::get('inventory-vendor-category/trash', [InventoryVendorCategoryController::class, 'trash'])->name('inventory-vendor-category.trash');
    Route::post('inventory-vendor-category/{id}/restore', [InventoryVendorCategoryController::class, 'restore'])->name('inventory-vendor-category.restore');
    Route::delete('inventory-vendor-category/{id}/force', [InventoryVendorCategoryController::class, 'force'])->name('inventory-vendor-category.force');
    Route::patch('inventory-vendor-category/{statuses}/status-toggle',[InventoryVendorCategoryController::class, 'toggleStatus'])->name('inventory-vendor-category.status.toggle');

    // Route::get('vendors', [VendorController::class, 'index'])->name('vendors.index');
    // Route::get('vendors/create', [VendorController::class, 'create'])->name('vendors.create');
    // Route::post('vendors/{vendor}/status', [VendorController::class, 'toggleStatus']);



    Route::get('inventory-vendors', [InventoryVendorController::class, 'index'])->name('inventory-vendors.index');
    Route::get('inventory-vendors/create', [InventoryVendorController::class, 'create'])->name('inventory-vendors.create');
    Route::post('inventory-vendors', [InventoryVendorController::class, 'store'])->name('inventory-vendors.store');
    Route::get('inventory-vendors/{user}/edit', [InventoryVendorController::class, 'edit'])->name('inventory-vendors.edit');
    Route::put('inventory-vendors/{user}', [InventoryVendorController::class, 'update'])->name('inventory-vendors.update');
    Route::post('inventory-vendors/{user}/status-toggle', [InventoryVendorController::class, 'toggleStatus'])->name('inventory-vendors.status.toggle');
    Route::delete('inventory-vendors/{user}', [InventoryVendorController::class, 'destroy'])->name('inventory-vendors.destroy');
    Route::get('inventory-vendors/trash', [InventoryVendorController::class, 'trash'])->name('inventory-vendors.trash');
    Route::post('inventory-vendors/{id}/restore', [InventoryVendorController::class, 'restore'])->name('inventory-vendors.restore');
    Route::get('inventory-vendors/{id}/force', [InventoryVendorController::class, 'force'])->name('inventory-vendors.force');
    Route::get('/inventory-vendors/{user}', [InventoryVendorController::class, 'show'])->name('inventory-vendors.show');

    /* PROJECT TYPES */
    Route::resource('lead-sources', LeadSourceController::class)->except(['show', 'create']);
    Route::get('lead-sources/trash', [LeadSourceController::class, 'trash'])->name('lead-sources.trash');
    Route::post('lead-sources/{id}/restore', [LeadSourceController::class, 'restore'])->name('lead-sources.restore');
    Route::delete('lead-sources/{id}/force', [LeadSourceController::class, 'force'])->name('lead-sources.force');
    Route::patch('lead-sources/{sources}/status-toggle',[LeadSourceController::class, 'toggleStatus'])->name('lead-sources.status.toggle');

    /* PROJECT STATUSES */
    Route::resource('lead-statuses', LeadStatusController::class)->except(['show', 'create']);
    Route::get('lead-statuses/trash', [LeadStatusController::class, 'trash'])->name('lead-statuses.trash');
    Route::post('lead-statuses/{id}/restore', [LeadStatusController::class, 'restore'])->name('lead-statuses.restore');
    Route::delete('lead-statuses/{id}/force', [LeadStatusController::class, 'force'])->name('lead-statuses.force');
    Route::patch('lead-statuses/{statuses}/status-toggle',[LeadStatusController::class, 'toggleStatus'])->name('lead-statuses.status.toggle');


    Route::get('leads', [LeadController::class, 'index'])->name('leads.index');
    Route::get('leads/create', [LeadController::class, 'create'])->name('leads.create');
    Route::post('leads', [LeadController::class, 'store'])->name('leads.store');
    Route::get('leads/{lead}/edit', [LeadController::class, 'edit'])->name('leads.edit');
    Route::put('leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
    Route::post('leads/{lead}/status-toggle', [LeadController::class, 'toggleStatus'])->name('leads.status.toggle');
    Route::delete('leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');
    Route::get('leads/trash', [LeadController::class, 'trash'])->name('leads.trash');
    Route::post('leads/{id}/restore', [LeadController::class, 'restore'])->name('leads.restore');
    Route::get('leads/{id}/force', [LeadController::class, 'force'])->name('leads.force');
    Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('leads.show');



    /* PROJECT STATUSES */
    Route::resource('task-statuses', TaskStatusController::class)->except(['show', 'create']);
    Route::get('task-statuses/trash', [TaskStatusController::class, 'trash'])->name('task-statuses.trash');
    Route::post('task-statuses/{id}/restore', [TaskStatusController::class, 'restore'])->name('task-statuses.restore');
    Route::delete('task-statuses/{id}/force', [TaskStatusController::class, 'force'])->name('task-statuses.force');
    Route::patch('task-statuses/{statuses}/status-toggle',[TaskStatusController::class, 'toggleStatus'])->name('task-statuses.status.toggle');

    /* PROJECT STATUSES */
    Route::resource('task-priorities', TaskPriorityController::class)->except(['show', 'create']);
    Route::get('task-priorities/trash', [TaskPriorityController::class, 'trash'])->name('task-priorities.trash');
    Route::post('task-priorities/{id}/restore', [TaskPriorityController::class, 'restore'])->name('task-priorities.restore');
    Route::delete('task-priorities/{id}/force', [TaskPriorityController::class, 'force'])->name('task-priorities.force');
    Route::patch('task-priorities/{priorities}/status-toggle',[TaskPriorityController::class, 'toggleStatus'])->name('task-priorities.status.toggle');


    Route::get('tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::post('tasks/{task}/status-toggle', [TaskController::class, 'toggleStatus'])->name('tasks.status.toggle');
    Route::delete('tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::get('tasks/trash', [TaskController::class, 'trash'])->name('tasks.trash');
    Route::post('tasks/{id}/restore', [TaskController::class, 'restore'])->name('tasks.restore');
    Route::delete('tasks/{id}/force', [TaskController::class, 'force'])->name('tasks.force');

    // Kanban & Calendar
    Route::get('tasks-kanban', [TaskController::class, 'kanban'])->name('tasks.kanban');
    Route::post('tasks/{task}/update-status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
    Route::get('tasks-calendar', [TaskController::class, 'calendar'])->name('tasks.calendar');
    Route::get('tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');


    Route::resource('inventory-categories', InventoryCategoryController::class)->except(['show', 'create']);
    Route::get('inventory-categories/trash', [InventoryCategoryController::class, 'trash'])->name('inventory-categories.trash');
    Route::post('inventory-categories/{id}/restore', [InventoryCategoryController::class, 'restore'])->name('inventory-categories.restore');
    Route::delete('inventory-categories/{id}/force', [InventoryCategoryController::class, 'force'])->name('inventory-categories.force');
    Route::patch('inventory-categories/{categories}/status-toggle',[InventoryCategoryController::class, 'toggleStatus'])->name('inventory-categories.status.toggle');
    Route::get('inventory-categories/{category}/children',[InventoryCategoryController::class, 'children'])->name('inventory-categories.children');


    Route::resource('inventory-units', InventoryUnitController::class)->except(['show', 'create']);
    Route::get('inventory-units/trash', [InventoryUnitController::class, 'trash'])->name('inventory-units.trash');
    Route::post('inventory-units/{id}/restore', [InventoryUnitController::class, 'restore'])->name('inventory-units.restore');
    Route::delete('inventory-units/{id}/force', [InventoryUnitController::class, 'force'])->name('inventory-units.force');
    Route::patch('inventory-units/{units}/status-toggle',[InventoryUnitController::class, 'toggleStatus'])->name('inventory-units.status.toggle');

    Route::resource('inventory-brands', InventoryBrandController::class)->except(['show', 'create']);
    Route::get('inventory-brands/trash', [InventoryBrandController::class, 'trash'])->name('inventory-brands.trash');
    Route::post('inventory-brands/{id}/restore', [InventoryBrandController::class, 'restore'])->name('inventory-brands.restore');
    Route::delete('inventory-brands/{id}/force', [InventoryBrandController::class, 'force'])->name('inventory-brands.force');
    Route::patch('inventory-brands/{brands}/status-toggle',[InventoryBrandController::class, 'toggleStatus'])->name('inventory-brands.status.toggle');


    Route::get('inventory-items', [InventoryItemController::class, 'index'])->name('inventory-items.index');
    Route::get('inventory-items/create', [InventoryItemController::class, 'create'])->name('inventory-items.create');
    Route::post('inventory-items', [InventoryItemController::class, 'store'])->name('inventory-items.store');
    Route::get('inventory-items/{inventoryItem}/edit', [InventoryItemController::class, 'edit'])->name('inventory-items.edit');
    Route::put('inventory-items/{inventoryItem}', [InventoryItemController::class, 'update'])->name('inventory-items.update');
    Route::post('inventory-items/{inventoryItem}/status-toggle', [InventoryItemController::class, 'toggleStatus'])->name('inventory-items.status.toggle');
    Route::delete('inventory-items/{inventoryItem}', [InventoryItemController::class, 'destroy'])->name('inventory-items.destroy');
    Route::get('inventory-items/trash', [InventoryItemController::class, 'trash'])->name('inventory-items.trash');
    Route::post('inventory-items/{id}/restore', [InventoryItemController::class, 'restore'])->name('inventory-items.restore');
    Route::delete('inventory-items/{id}/force', [InventoryItemController::class, 'force'])->name('inventory-items.force');
    Route::get('inventory/get-sub-categories',[InventoryItemController::class, 'getSubCategories'])->name('inventory.get-sub-categories');
    Route::get('/inventory-items/{inventoryItem}', [InventoryItemController::class, 'show'])->name('inventory-items.show');
    
    Route::resource('invoices', InvoiceController::class);
    Route::get('invoices/{invoice}/edit', [InvoiceController::class,'edit'])->name('invoices.edit');
    Route::put('invoices/{invoice}', [InvoiceController::class,'update'])->name('invoices.update');
    Route::post('invoices/{invoice}/cancel',[InvoiceController::class, 'cancel'])->name('invoices.cancel');
    Route::get('invoices/{invoice}/pdf',[InvoiceController::class, 'pdf'])->name('invoices.pdf');

    //Route::post('credit-notes', [CreditNoteController::class, 'store'])->name('credit-notes.store');
    Route::post('credit-notes/store', [CreditNoteController::class, 'store'])->name('credit-notes.store');
    Route::get('credit-notes/create/{invoice}',[CreditNoteController::class, 'create'])->name('credit-notes.create');


    // // Show form to create a new credit note for an invoice
    // Route::get('invoices/{invoice}/credit-notes/create', [CreditNoteController::class, 'create'])->name('credit-notes.create');
    // // Store new credit note
    // Route::post('credit-notes', [CreditNoteController::class, 'store'])->name('credit-notes.store');
    // Edit existing credit note
    Route::get('credit-notes/{creditNote}/edit', [CreditNoteController::class, 'edit'])->name('credit-notes.edit');
    // Update credit note
    Route::put('credit-notes/{creditNote}', [CreditNoteController::class, 'update'])->name('credit-notes.update');
    // Cancel credit note
    Route::post('credit-notes/{creditNote}/cancel', [CreditNoteController::class, 'cancel'])->name('credit-notes.cancel');

    Route::get('credit-notes/{credit_note}/pdf', [CreditNoteController::class, 'downloadPdf'])->name('credit-notes.pdf');
    Route::post('credit-notes/{creditNote}/reversal', [CreditNoteController::class, 'reversal'])->name('credit-notes.reversal');



    Route::post('invoices/{invoice}/payment',[PaymentController::class,'store'])->name('invoices.payment');
    Route::get('reports',[ReportController::class,'index'])->name('reports');


    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    Route::get('reports/gstr1', [Gstr1Controller::class, 'report'])->name('reports.gstr1');


    Route::get('invoices/{invoice}/ledger',[InvoiceController::class, 'ledger'])->name('invoices.ledger');

    Route::get('payments/{payment}/receipt',[PaymentController::class, 'downloadReceipt'])->name('payments.receipt');

    Route::get('clients/{client}/ledger',[ClientLedgerController::class, 'index'])->name('clients.ledger');
    // web.php or admin.php routes file
    Route::post('invoices/{invoice}/finalize', [InvoiceController::class, 'finalize'])->name('invoices.finalize');

    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::get('purchase-orders/{id}/approve',[PurchaseOrderController::class, 'approve'])->name('purchase-orders.approve');
    Route::get('get-vendor-items/{vendor}',[PurchaseOrderController::class, 'getVendorItems']);

     Route::get('get-vendor-items/{vendor}',[PurchaseOrderController::class, 'getVendorItems'])->name('get.vendor.items');

    Route::get('purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
    Route::get('purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('purchase-orders.create');
    Route::post('purchase-orders/store', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
    Route::get('purchase-orders/{id}/approve', [PurchaseOrderController::class, 'approve'])->name('purchase-orders.approve');

    Route::get('purchase-receipts/create', [GoodsReceiptController::class, 'create'])->name('purchase-receipts.create');
    Route::post('purchase-receipts/store', [GoodsReceiptController::class, 'store'])->name('purchase-receipts.store');
    Route::resource('purchase-receipts', GoodsReceiptController::class);

    Route::resource('expenses', ExpenseController::class);

    Route::resource('expense-categories', ExpenseCategoryController::class)->except(['show', 'create']);
    Route::get('expense-categories/trash', [ExpenseCategoryController::class, 'trash'])->name('expense-categories.trash');
    Route::post('expense-categories/{id}/restore', [ExpenseCategoryController::class, 'restore'])->name('expense-categories.restore');
    Route::delete('expense-categories/{id}/force', [ExpenseCategoryController::class, 'force'])->name('expense-categories.force');
    Route::patch('expense-categories/{brands}/status-toggle',[ExpenseCategoryController::class, 'toggleStatus'])->name('expense-categories.status.toggle');

    Route::get('reports/financial-summary', [FinancialReportController::class, 'index'])->name('reports.financial.summary');
    Route::get('reports/monthly-profit', [FinancialReportController::class, 'monthlyProfit'])->name('reports.monthly_profit');
    Route::get('reports/expense-by-category', [FinancialReportController::class, 'expenseByCategory'])->name('reports.expense_category');
    Route::get('reports/tax-summary', [FinancialReportController::class, 'taxSummary'])->name('reports.tax_summary');
    Route::get('reports/outstanding', [FinancialReportController::class, 'outstandingReceivables'])->name('reports.outstanding');
    Route::get('reports/cashflow', [FinancialReportController::class, 'cashflow'])->name('reports.cashflow');
    Route::get('reports/balance-sheet', [FinancialReportController::class, 'balanceSheet'])->name('reports.balance.sheet');


    Route::get('reports/profit-loss',[AnalyticsController::class,'profitLoss'])->name('reports.profit.loss');
    Route::get('reports/revenue-chart',[AnalyticsController::class,'revenueChart'])->name('reports.revenue.chart');
    Route::get('reports/profit-product',[AnalyticsController::class,'profitByProduct'])->name('reports.profit.product');
    Route::get('reports/profit-client',[AnalyticsController::class,'profitByClient'])->name('reports.profit.client');
    Route::get('reports/gst-wise',[AnalyticsController::class,'gstWiseRevenue'])->name('reports.gst.wise');
    Route::get('reports/profit-loss-export',[AnalyticsController::class,'exportProfitLoss'])->name('reports.profit.loss.export');
    Route::get('reports/year-comparison',[AnalyticsController::class,'yearlyComparison'])->name('reports.year_comparison');
    Route::get('reports/inventory-valuation',[AnalyticsController::class,'inventoryValuation'])->name('reports.inventory.valuation');
    Route::get('reports/aging-report',[AnalyticsController::class,'agingReport'])->name('reports.aging_report');
    Route::get('reports/inventory/movement/{id}', [AnalyticsController::class, 'movementReport'])->name('reports.inventory.movement');

    Route::get('reports/stock-aging',[AnalyticsController::class,'stockAging'])->name('reports.stock.aging');
    Route::get('reports/low-stock',[AnalyticsController::class,'lowStock'])->name('reports.low.stock');
    Route::get('reports/daily-closing',[AnalyticsController::class,'dailyClosing'])->name('reports.daily.closing');
    Route::get('reports/dead-stock',[AnalyticsController::class,'deadStock'])->name('reports.dead.stock');


    

});

Route::post('/admin/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('admin.logout');

Route::get('/gstr1', [Gstr1Controller::class,'index']);       // JSON view
Route::get('/gstr1/export', [Gstr1Controller::class,'export']); // Excel export


require __DIR__.'/auth.php';
