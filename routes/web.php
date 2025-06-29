<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->to('/admin');
});

Route::get('invoice/{id}/{year}/{month}', [App\Http\Controllers\InvoiceController::class, 'index'])->name('invoice.index');
