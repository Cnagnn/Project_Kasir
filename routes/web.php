<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\StockBatchController;

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/', [loginController::class, "showLoginForm"])->name('login');
Route::post('/', [loginController::class, "loginProcess"])->name('login.process');

// Route::resource('/dashboard', ProductController::class);


Route::middleware(['auth', 'checkrole:Manager,Cashier'])->group(function () {
    Route::get('/products', [ProductController::class, "index"])->name('product.index');
    Route::get('/product/search', [ProductController::class, 'search'])->name('product.search');

    Route::get('/categories', [CategoryController::class, "index"])->name('category.index');
    Route::get('/category/search', [CategoryController::class, 'search'])->name('category.search');

    Route::get('/category/{id}/detail', [CategoryController::class, 'productsByCategory'])->name('category.detail');
    Route::get('/category/product/{id}/detail', [CategoryController::class, 'categoryProductDetail'])->name('category.productDetail');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::get('/cart/remove_cart', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('/product/add-to-cart', [CartController::class, 'addToCart'])->name('cart.addToCart');
    Route::post('/product/increase-qty-cart', [CartController::class, 'increaseQtyCart'])->name('cart.increaseQtyCart');
    Route::post('/product/decrease-qty-cart', [CartController::class, 'decreaseQtyCart'])->name('cart.decreaseQtyCart');
});


Route::middleware(['auth', 'checkrole:Manager'])->group(function () {
    // Route::get('/products', [ProductController::class, "index"])->name('product.index');
    Route::post('/product-add', [ProductController::class, "store"])->name('product.store');
    Route::get('/product/{id}/edit', [ProductController::class, 'edit'])->name('product.edit');
    Route::put('/product/{id}/update', [ProductController::class, "update"])->name('product.update');
    Route::delete('/product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');
    // Route::get('/product/search', [ProductController::class, 'search'])->name('product.search');

    Route::post('/batch-add', [StockBatchController::class, "store"])->name('stock_batches.store');
    Route::put('/batch/{id}/update', [StockBatchController::class, "edit"])->name('stock_batches.update');
    Route::delete('/batch/{id}', [StockBatchController::class, "destroy"])->name('stock_batches.destroy');

    // Route::get('/categories', [CategoryController::class, "index"])->name('category.index');
    Route::post('/category-add', [CategoryController::class, "store"])->name('category.store');
    Route::get('/category/{id}/edit', [CategoryController::class, 'edit'])->name('category.edit');
    Route::put('/category/{id}/update', [CategoryController::class, "update"])->name('category.update');
    Route::delete('/category/{id}', [CategoryController::class, 'destroy'])->name('category.destroy');
    // Route::get('/category/search', [CategoryController::class, 'search'])->name('category.search');

    // Route::get('/category/{id}/detail', [CategoryController::class, 'productsByCategory'])->name('category.detail');
    // Route::get('/category/product/{id}/edit', [CategoryController::class, 'categoryProductDetail'])->name('category.productDetail');

    // Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    // Route::get('/cart/remove_cart', [CartController::class, 'destroy'])->name('cart.destroy');
    // Route::post('/product/add-to-cart', [CartController::class, 'addToCart'])->name('cart.addToCart');
    // Route::post('/product/increase-qty-cart', [CartController::class, 'increaseQtyCart'])->name('cart.increaseQtyCart');
    // Route::post('/product/decrease-qty-cart', [CartController::class, 'decreaseQtyCart'])->name('cart.decreaseQtyCart');

    Route::get('/employee', [EmployeeController::class, 'index'])->name('employee.index');
    Route::post('/employee-add', [EmployeeController::class, 'store'])->name('employee.store');
    Route::post('/employee/{id}/update', [EmployeeController::class, 'update'])->name('employee.update');
    Route::get('/employee/{id}/detail', [EmployeeController::class, 'detail'])->name('employee.detail');
    

    Route::post('/role-add', [RoleController::class, 'store'])->name('role.store');

});

