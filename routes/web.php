<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

// Route::resource('/dashboard', ProductController::class);
Route::get('/product', [ProductController::class, "index"])->name('products.index');
Route::post('/product-add', [ProductController::class, "store"])->name('product.store');
Route::put('/product-update/{id}', [ProductController::class, "update"])->name('product.update');
Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

Route::post('/category-add', [CategoryController::class, "store"])->name('category.store');