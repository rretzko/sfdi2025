<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('welcome');

Route::view('dashboard', 'bio')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'bio')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
