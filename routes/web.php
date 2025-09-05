<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\CompanyController;

Route::get('/', function () {
    return view('welcome');
});

// Company switching routes
Route::post('/company/switch', [CompanyController::class, 'switch'])->name('company.switch');
Route::get('/company/current', [CompanyController::class, 'getCurrentCompany'])->name('company.current');

// Voucher PDF download route
Route::get('/voucher/{voucherId}/download-pdf', [VoucherController::class, 'generatePDF'])->name('voucher.download-pdf');
