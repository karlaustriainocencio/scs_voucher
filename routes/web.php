<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VoucherController;

Route::get('/', function () {
    return view('welcome');
});

// Voucher PDF download route
Route::get('/voucher/download-pdf/{voucherId}', [VoucherController::class, 'generatePDF'])->name('voucher.download-pdf');
