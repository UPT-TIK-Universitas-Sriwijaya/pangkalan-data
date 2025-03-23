<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Configuration;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::group(['middleware' => ['role:admin']], function() {

        Route::get('configuration', Configuration::class)->name('configuration');
        Route::get('users', \App\Livewire\Users\Index::class)->name('users');
        Route::get('users/create', \App\Livewire\Users\Create::class)->name('users.create');

        Route::prefix('sister')->group(function () {
            Route::get('configuration', \App\Livewire\Sister\Configuration::class)->name('sister.configuration');
        });

        Route::prefix('feeder')->group(function () {
            Route::get('sinkronisasi', \App\Livewire\Feeder\Sinkronisasi\Index::class)->name('feeder.sinkronisasi');
        });

    });


});

require __DIR__.'/auth.php';
