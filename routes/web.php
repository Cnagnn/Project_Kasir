<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SellingController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PurchasingController;
use App\Http\Controllers\StockBatchController;
use App\Http\Controllers\TransactionHistoryController;

Route::get('/dashboard', [DashboardController::class, "index"])->name('dashboard.index');

Route::get('/', [loginController::class, "showLoginForm"])->name('login');
Route::post('/', [loginController::class, "loginProcess"])->name('login.process');
Route::post('/logout', [loginController::class, "logout"])->name('logout');

// Route::resource('/dashboard', ProductController::class);


Route::middleware(['auth', 'checkrole:Manager,Cashier'])->group(function () {
    // Route::post('/payment-webhook', [WebhookController::class, 'handle']);

    Route::get('/products', [ProductController::class, "index"])->name('product.index');
    Route::get('/product/search', [ProductController::class, 'search'])->name('product.search');

    Route::get('/categories', [CategoryController::class, "index"])->name('category.index');
    Route::get('/category/search', [CategoryController::class, 'search'])->name('category.search');

    Route::get('/category/{id}/detail', [CategoryController::class, 'productsByCategory'])->name('category.detail');
    Route::get('/category/product/{id}/detail', [CategoryController::class, 'categoryProductDetail'])->name('category.productDetail');

    Route::get('/item-stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('/item-stock/detail/{id}', [StockController::class, 'detail'])->name('stock.detail');

    Route::get('/Selling', [SellingController::class, 'index'])->name('selling.index');
    Route::get('/Selling/products/search', [SellingController::class, 'search'])->name('selling.products.search');

    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('cart.checkout.process');
    Route::get('/selling/receipt/{id}', [CheckoutController::class, 'receipt'])->name('selling.receipt');

    Route::get('/cart/items', [CartController::class, 'getCartItems'])->name('cart.items');
    Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/remove', [CartController::class, 'removeFromCart'])->name('cart.remove');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::get('/cart/remove_cart', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('/product/add-to-cart', [CartController::class, 'addToCart'])->name('cart.addToCart');
    Route::post('/cart/increase-qty-cart', [CartController::class, 'increaseQtyCart'])->name('cart.increaseQtyCart');
    Route::post('/cart/decrease-qty-cart', [CartController::class, 'decreaseQtyCart'])->name('cart.decreaseQtyCart');
    Route::post('/cart/update-qty-cart', [CartController::class, 'updateQtyCart'])->name('cart.updateQtyCart');

    Route::get('/transaction_history', [TransactionHistoryController::class, 'index'])->name('transactionHistory.index');
    Route::get('/transaction_history/get_data', [TransactionHistoryController::class, 'getTransactionHistory'])->name('transactionHistory.getTransactionHistory');
    Route::get('/transaction_history/detail/{id}', [TransactionHistoryController::class, 'detail'])->name('transactionHistory.detail');
    Route::put('/transaction_history/detail/update/{id}', [TransactionHistoryController::class, 'updateDetail'])->name('transactionHistory.updateDetail');
    Route::delete('/transaction_history/detail/delete/{id}', [TransactionHistoryController::class, 'deleteDetail'])->name('transactionHistory.deleteDetail');
    Route::get('/transaction_history/print/{id}', [TransactionHistoryController::class, 'print'])->name('transactionHistory.print');

    // Data grafik penjualan (JSON)
    Route::get('/dashboard/sales-data', [DashboardController::class, 'salesData'])->name('dashboard.salesData');
    Route::get('/dashboard/sales-product-data', [DashboardController::class, 'salesProductData'])->name('dashboard.salesProductData');
    Route::get('/dashboard/metrics', [DashboardController::class, 'metrics'])->name('dashboard.metrics');
    Route::get('/dashboard/category-data', [DashboardController::class, 'categoryData'])->name('dashboard.categoryData');
    // Halaman Laporan
    Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    // Laporan Stock (printable)
    Route::get('/reports/stock/print', [\App\Http\Controllers\ReportController::class, 'printStock'])->name('reports.stock.print');
    // Laporan Pendapatan per Invoice (PDF)
    Route::get('/reports/invoice-revenue/print', [\App\Http\Controllers\ReportController::class, 'printInvoiceRevenue'])->name('reports.invoiceRevenue.print');
    // Laporan Pendapatan per Produk (PDF)
    Route::get('/reports/product-revenue/print', [\App\Http\Controllers\ReportController::class, 'printProductRevenue'])->name('reports.productRevenue.print');
    // Laporan Pembelian (PDF)
    Route::get('/reports/purchasing/print', [\App\Http\Controllers\ReportController::class, 'printPurchasing'])->name('reports.purchasing.print');
});


Route::middleware(['auth', 'checkrole:Manager'])->group(function () {
    // Route::get('/products', [ProductController::class, "index"])->name('product.index');
    Route::post('/product-add', [ProductController::class, "store"])->name('product.store');
    Route::get('/product/{id}/edit', [ProductController::class, 'edit'])->name('product.edit');
    Route::post('/product/update', [ProductController::class, "update"])->name('product.update');
    Route::delete('/product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');
    // Route::get('/product/search', [ProductController::class, 'search'])->name('product.search');

    Route::get('/purchasing', [PurchasingController::class, 'index'])->name('purchasing.index');
    Route::get('/purchasing/products/search', [PurchasingController::class, 'search'])->name('purchasing.products.search');
    Route::get('/purchasing/products/find-by-name', [PurchasingController::class, 'findByName'])->name('purchasing.products.findByName');
    Route::post('/purchasing/add', [PurchasingController::class, 'add'])->name('purchasing.stock.in.add');
    Route::post('/purchasing/process', [PurchasingController::class, 'process'])->name('purchasing.stock.in.process');

    // Route::get('/Selling', [SellingController::class, 'index'])->name('selling.index');
    // Route::get('/Selling/products/search', [SellingController::class, 'search'])->name('selling.products.search');
    // Route::post('/Selling/process', [SellingController::class, 'store'])->name('selling.products.process');

    Route::post('/batch-add', [StockBatchController::class, "store"])->name('stock_batches.store');
    Route::put('/stock/{id}/update', [StockController::class, "edit"])->name('stock.update');
    Route::delete('/batch/{id}', [StockBatchController::class, "destroy"])->name('stock_batches.destroy');

    // Route::get('/purchasing', [PurchasingController::class, "index"])->name('purchasing.index');

    // Route::get('/categories', [CategoryController::class, "index"])->name('category.index');
    Route::post('/category-add', [CategoryController::class, "store"])->name('category.store');
    Route::get('/category/{id}/edit', [CategoryController::class, 'edit'])->name('category.edit');
    Route::post('/category/update', [CategoryController::class, "update"])->name('category.update');
    Route::delete('/category/{id}', [CategoryController::class, 'destroy'])->name('category.destroy');
    Route::post('/category/{id}/archive', [CategoryController::class, 'archive'])->name('category.archive');
    // Route::get('/category/search', [CategoryController::class, 'search'])->name('category.search');

    // Route::get('/category/{id}/detail', [CategoryController::class, 'productsByCategory'])->name('category.detail');
    // Route::get('/category/product/{id}/edit', [CategoryController::class, 'categoryProductDetail'])->name('category.productDetail');

    // Route::get('/cart/items', [CartController::class, 'getCartItems'])->name('cart.items');
    // Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
    // Route::post('/cart/remove', [CartController::class, 'removeFromCart'])->name('cart.remove');
    // Route::post('/cart/process', [CartController::class, 'store'])->name('cart.checkout.process');
    // Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    // Route::get('/cart/remove_cart', [CartController::class, 'destroy'])->name('cart.destroy');
    // Route::post('/product/add-to-cart', [CartController::class, 'addToCart'])->name('cart.addToCart');
    // Route::post('/product/increase-qty-cart', [CartController::class, 'increaseQtyCart'])->name('cart.increaseQtyCart');
    // Route::post('/product/decrease-qty-cart', [CartController::class, 'decreaseQtyCart'])->name('cart.decreaseQtyCart');

    // Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('cart.checkout.process');

    Route::get('/employee', [EmployeeController::class, 'index'])->name('employee.index');
    Route::post('/employee-add', [EmployeeController::class, 'store'])->name('employee.store');
    Route::post('/employee/update', [EmployeeController::class, 'update'])->name('employee.update');
    Route::delete('/employee/{id}', [EmployeeController::class, 'destroy'])->name('employee.destroy');
    Route::get('/employee/{id}/detail', [EmployeeController::class, 'detail'])->name('employee.detail');
    Route::get('/employee/search', [EmployeeController::class, 'search'])->name('employee.search');

    Route::get('/role', [RoleController::class, 'index'])->name('role.index');
    Route::post('/role/add', [RoleController::class, 'store'])->name('role.store');
    Route::post('/role/update', [RoleController::class, 'update'])->name('role.update');
    Route::delete('/role/delete/{id}', [RoleController::class, 'destroy'])->name('role.destroy');
    Route::get('/role/search', [RoleController::class, 'search'])->name('role.search');

});

