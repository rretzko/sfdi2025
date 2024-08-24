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

require __DIR__.'/auth.php';
