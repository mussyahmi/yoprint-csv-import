<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ProductController;

Route::get('/', [UploadController::class, 'index']);
Route::post('/upload', [UploadController::class, 'store']);
Route::get('/uploads', [UploadController::class, 'uploads']);
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
