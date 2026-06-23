<?php

use App\Http\Controllers\Auth\PostLoginRedirectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RelationController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\SiteSettingsController;
use App\Http\Controllers\ThingController;
use App\Http\Controllers\WikidataThingController;
use App\Http\Controllers\WikidataTypeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

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
Route::get('auth/redirect', PostLoginRedirectController::class)
    ->middleware(['auth'])
    ->name('auth.redirect');

Route::get('dashboard', DashboardController::class)
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('settings.profile');
    Route::put('settings/profile', [ProfileController::class, 'update'])->name('settings.profile.update');
    Route::get('settings/site', [SiteSettingsController::class, 'edit'])->name('settings.site');
    Route::put('settings/site', [SiteSettingsController::class, 'update'])->name('settings.site.update');

    Route::resource('things', ThingController::class)->only(['index']);
    Route::resource('properties', PropertyController::class)->only(['index']);
    Route::resource('relations', RelationController::class)->only(['index']);
    Route::resource('messages', MessageController::class)->only(['index']);

    Route::post('items/from-photo', [ItemController::class, 'storeFromPhoto'])
        ->middleware(['auth0.verified'])
        ->name('items.from-photo.store');
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

Route::get('/{namespace}/{slug}', [ItemController::class, 'showBySemantic'])
    ->where([
        'namespace' => '^(?!auth$|dashboard$|settings$|things$|properties$|relations$|messages$|wd$)[A-Za-z][A-Za-z0-9-]*$',
        'slug' => '[A-Za-z0-9\-\._~%]+',
    ])
    ->name('items.semantic.show');

Route::get('/{slug}', [ThingController::class, 'showBySlug'])
    ->where('slug', '^(?![0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$)(?!dashboard$|settings$|things$|properties$|relations$|messages$)[A-Za-z0-9\-\._~%]+$')
    ->name('things.show-by-slug');

Route::get('/{uuid}', [ItemController::class, 'show'])
    ->whereUuid('uuid')
    ->name('items.show');
