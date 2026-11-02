<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/products');

Route::patch('products/{product}/quantity', [ProductController::class, 'adjustQuantity'])
    ->name('products.adjust-quantity');

Route::resource('products', ProductController::class);
