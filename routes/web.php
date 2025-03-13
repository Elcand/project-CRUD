<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {})->name('home');

Route::get('customers/trash', [CustomerController::class, 'trashIndex'])->name('customers.trash');
Route::get('customers/restore/{customer}', [CustomerController::class, 'restore'])->name('customers.store');
Route::resource('customers', CustomerController::class);
