<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StockBatchController;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

// Route::resource('/dashboard', ProductController::class);
Route::get('/products', [ProductController::class, "index"])->name('product.index');
Route::post('/product-add', [ProductController::class, "store"])->name('product.store');
Route::get('/product/{id}/edit', [ProductController::class, 'edit'])->name('product.edit');
Route::put('/product/{id}/update', [ProductController::class, "update"])->name('product.update');
Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('product.destroy');

Route::get('/product/search', [ProductController::class, 'search'])->name('product.search');

Route::post('/batch-add', [StockBatchController::class, "store"])->name('stock_batches.store');
Route::put('/batch/{id}/update', [StockBatchController::class, "edit"])->name('stock_batches.update');
Route::delete('/batch/{id}', [StockBatchController::class, "destroy"])->name('stock_batches.destroy');

Route::post('/category-add', [CategoryController::class, "store"])->name('category.store');