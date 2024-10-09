<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('welcome');

Route::view('dashboard', 'bio')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'bio')
    ->middleware(['auth'])
    ->name('profile');

Route::view('school', 'school')
    ->middleware(['auth'])
    ->name('school');

Route::view('emergencyContacts', 'emergencyContacts')
    ->middleware(['auth'])
    ->name('emergencyContacts');

Route::view('events', 'events')
    ->middleware(['auth'])
    ->name('events');

//PDFs
Route::get('pdf/application/{candidate}', \App\Http\Controllers\Pdfs\ApplicationPdfController::class)
    ->name('pdf.application');

//Square Payment
Route::get('square/{candidateId}/{amountDue}', \App\Http\Controllers\Square\SquarePaymentController::class)
    ->name('square');

require __DIR__.'/auth.php';
