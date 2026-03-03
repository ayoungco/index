<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('dashboard', function () {
    $things = Thing::query()
        ->latest('created_at')
        ->get();

    return view('dashboard', compact('things'));
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
    Route::resource('things', ThingController::class);
    Route::post('things/{thing}/scan-photo', [ThingController::class, 'storeScanPhoto'])->name('things.scan-photo');
    Route::resource('properties', PropertyController::class);
    Route::resource('relations', RelationController::class);
    Route::resource('messages', MessageController::class);

    Route::get('/wd/thing/{qid}', [WikidataThingController::class, 'show'])->name('wd.thing.show');
    Route::get('/wd/{type}', [WikidataTypeController::class, 'index'])->name('wd.type.index');
    Route::get('/{uuid}/print', [ScannedItemController::class, 'print'])
        ->whereUuid('uuid')
        ->name('scanned-items.print');
});

Route::post('/{uuid}/initialize', [ScannedItemController::class, 'initialize'])
    ->whereUuid('uuid')
    ->middleware(['auth', 'auth0.verified'])
    ->name('scanned-items.initialize');

Route::post('/{uuid}/events', [ScannedItemController::class, 'addPhoto'])
    ->whereUuid('uuid')
    ->middleware(['auth', 'auth0.verified'])
    ->name('scanned-items.events.store');

Route::get('/{uuid}', [ScannedItemController::class, 'show'])
    ->whereUuid('uuid')
    ->name('scanned-items.show');

Route::middleware(['auth', 'verified.email'])->group(function () {
    Route::post('/{uuid}/initialize', [ItemController::class, 'storeInitialized'])
        ->whereUuid('uuid')
        ->name('items.initialize.store');

    Route::post('/{uuid}/photo', [ItemController::class, 'storePhoto'])
        ->whereUuid('uuid')
        ->name('items.photo.store');
});

Route::get('/{uuid}/print', [ItemController::class, 'printLabel'])
    ->whereUuid('uuid')
    ->name('items.print');

Route::get('/{uuid}', [ItemController::class, 'showByUuid'])
    ->whereUuid('uuid')
    ->name('items.lookup');
