<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RelationController;
use App\Http\Controllers\ThingController;
use App\Http\Controllers\WikidataThingController;
use App\Http\Controllers\WikidataTypeController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
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
    Route::resource('properties', PropertyController::class);
    Route::resource('relations', RelationController::class);
    Route::resource('messages', MessageController::class);

    Route::get('/wd/thing/{qid}', [WikidataThingController::class, 'show'])->name('wd.thing.show');
    Route::get('/wd/{type}', [WikidataTypeController::class, 'index'])->name('wd.type.index');
});

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
