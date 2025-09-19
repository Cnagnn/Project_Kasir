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
Route::delete('/product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');
Route::get('/product/search', [ProductController::class, 'search'])->name('product.search');

Route::post('/batch-add', [StockBatchController::class, "store"])->name('stock_batches.store');
Route::put('/batch/{id}/update', [StockBatchController::class, "edit"])->name('stock_batches.update');
Route::delete('/batch/{id}', [StockBatchController::class, "destroy"])->name('stock_batches.destroy');

Route::get('/categories', [CategoryController::class, "index"])->name('category.index');
Route::post('/category-add', [CategoryController::class, "store"])->name('category.store');
Route::get('/category/{id}/edit', [CategoryController::class, 'edit'])->name('category.edit');
Route::put('/category/{id}/update', [CategoryController::class, "update"])->name('category.update');
Route::delete('/category/{id}', [CategoryController::class, 'destroy'])->name('category.destroy');
Route::get('/category/search', [CategoryController::class, 'search'])->name('category.search');

Route::get('/category/{id}/detail', [CategoryController::class, 'productsByCategory'])->name('category.detail');
Route::get('/category/product/{id}/edit', [CategoryController::class, 'categoryProductDetail'])->name('category.productDetail');
