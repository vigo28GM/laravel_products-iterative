<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductTagController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/products');

Route::patch('products/{product}/quantity', [ProductController::class, 'adjustQuantity'])
    ->name('products.adjust-quantity');

Route::get('tags', [TagController::class, 'index'])->name('tags.index');
Route::put('products/{product}/tags', [ProductTagController::class, 'update'])
    ->name('products.tags.update');

Route::resource('products', ProductController::class);
