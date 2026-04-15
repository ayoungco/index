<?php

use App\Http\Controllers\InstallerController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\WikidataThingController;
use App\Http\Controllers\WikidataTypeController;
use App\Models\Item;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;


Route::get('install', [InstallerController::class, 'show'])->name('install.show');
Route::post('install', [InstallerController::class, 'store'])->name('install.store');

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::prefix('wd')->name('wikidata.')->group(function () {
    Route::get('/item/{qid}', [WikidataThingController::class, 'show'])
        ->where('qid', 'Q[0-9]+')
        ->name('item.show');

    Route::get('/type/{type}', [WikidataTypeController::class, 'index'])
        ->where('type', '[A-Za-z-]+')
        ->name('type.index');
});

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
