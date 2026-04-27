<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\SiteSettingsController;
use App\Http\Controllers\Auth\PostLoginRedirectController;
use App\Models\Item;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('auth/redirect', PostLoginRedirectController::class)
    ->middleware(['auth'])
    ->name('auth.redirect');

Route::get('dashboard', function () {
    $items = Item::query()
        ->latest('created_at')
        ->get();

    return view('dashboard', compact('items'));
})
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
    Route::get('settings/site', [SiteSettingsController::class, 'edit'])->name('settings.site');
    Route::put('settings/site', [SiteSettingsController::class, 'update'])->name('settings.site.update');
});

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::get('/{uuid}/print', [ItemController::class, 'print'])
        ->whereUuid('uuid')
        ->middleware(['auth0.authenticate'])
        ->name('items.print');
});

Route::post('/{uuid}/initialize', [ItemController::class, 'initialize'])
    ->whereUuid('uuid')
    ->middleware(['auth0.authenticate', 'auth0.verified'])
    ->name('items.initialize');

Route::post('/{uuid}/events', [ItemController::class, 'addPhoto'])
    ->whereUuid('uuid')
    ->middleware(['auth0.authenticate', 'auth0.verified'])
    ->name('items.events.store');

Route::get('/{uuid}', [ItemController::class, 'show'])
    ->whereUuid('uuid')
    ->name('items.show');
