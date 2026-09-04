<?php

use App\Http\Controllers\Auth\PostLoginRedirectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LabelSheetController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProtectedMediaController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\SiteSettingsController;
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

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', DashboardController::class)
        ->name('dashboard');

    Route::get('dashboard/search', [DashboardController::class, 'search'])
        ->name('dashboard.search');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('settings.profile');
    Route::put('settings/profile', [ProfileController::class, 'update'])->name('settings.profile.update');
    Route::get('settings/site', [SiteSettingsController::class, 'edit'])->name('settings.site');
    Route::put('settings/site', [SiteSettingsController::class, 'update'])->name('settings.site.update');

    Route::resource('messages', MessageController::class)->only(['index']);

    Route::post('items/from-photo', [ItemController::class, 'storeFromPhoto'])
        ->middleware(['auth0.verified'])
        ->name('items.from-photo.store');

    Route::get('labels/print', [LabelSheetController::class, 'create'])->name('labels.create');
    Route::post('labels/print', [LabelSheetController::class, 'print'])->name('labels.print');
});

require __DIR__.'/auth.php';

Route::get('media/{path}', [ProtectedMediaController::class, 'show'])
    ->where('path', '.*')
    ->middleware(['auth0.authenticate.optional'])
    ->name('media.show');

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

Route::patch('/{uuid}/visibility', [ItemController::class, 'updateVisibility'])
    ->whereUuid('uuid')
    ->middleware(['auth0.authenticate', 'auth0.verified'])
    ->name('items.visibility.update');

Route::post('/{uuid}/events', [ItemController::class, 'addPhoto'])
    ->whereUuid('uuid')
    ->middleware(['auth0.authenticate', 'auth0.verified'])
    ->name('items.events.store');

Route::post('/{uuid}/featured-photo/{event}', [ItemController::class, 'featurePhoto'])
    ->whereUuid('uuid')
    ->middleware(['auth0.authenticate', 'auth0.verified'])
    ->name('items.featured-photo.update');

Route::post('/{uuid}/location', [ItemController::class, 'recordLocation'])
    ->whereUuid('uuid')
    ->middleware(['auth0.authenticate'])
    ->name('items.location.store');

Route::patch('/{uuid}', [ItemController::class, 'update'])
    ->whereUuid('uuid')
    ->middleware(['auth0.authenticate', 'auth0.verified'])
    ->name('items.update');

Route::get('/{namespace}/{slug}', [ItemController::class, 'showBySemantic'])
    ->where([
        'namespace' => '^(?!auth$|dashboard$|settings$|things$|properties$|relations$|messages$|wd$)[A-Za-z][A-Za-z0-9-]*$',
        'slug' => '[A-Za-z0-9_\-\._~%]+',
    ])
    ->middleware(['auth0.authenticate.optional'])
    ->name('items.semantic.show');

Route::get('/{identifier}', [ItemController::class, 'showByIdentifier'])
    ->where('identifier', '[A-Za-z][A-Za-z0-9_]*_[A-Za-z0-9_]+')
    ->middleware(['auth0.authenticate.optional'])
    ->name('items.identifier.show');

Route::get('/{uuid}', [ItemController::class, 'show'])
    ->whereUuid('uuid')
    ->middleware(['auth0.authenticate.optional'])
    ->name('items.show');
