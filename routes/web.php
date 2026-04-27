<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\WikidataThingController;
use App\Http\Controllers\WikidataTypeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RelationController;
use App\Http\Controllers\SiteSettingsController;
use App\Http\Controllers\Auth\PostLoginRedirectController;
use App\Http\Controllers\ThingController;
use App\Models\Item;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

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
Route::get('auth/redirect', PostLoginRedirectController::class)
    ->middleware(['auth'])
    ->name('auth.redirect');

Route::get('dashboard', function () {
    $search = trim((string) request()->query('q', ''));

    $items = Item::query()
        ->when($search !== '', function (Builder $query) use ($search) {
            $driver = $query->getConnection()->getDriverName();

            if (in_array($driver, ['mysql', 'pgsql'], true)) {
                $query->whereFullText(['name', 'description'], $search);

                return;
            }

            $query->where(function (Builder $likeQuery) use ($search) {
                $likeQuery
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        })
        ->latest('created_at')
        ->limit(100)
        ->get(['id', 'uuid', 'name', 'description', 'created_at']);

    return view('dashboard', compact('items', 'search'));
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

    Route::resource('things', ThingController::class)->only(['index']);
    Route::resource('properties', PropertyController::class)->only(['index']);
    Route::resource('relations', RelationController::class)->only(['index']);
    Route::resource('messages', MessageController::class)->only(['index']);
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

Route::get('/{slug}', [ThingController::class, 'showBySlug'])
    ->where('slug', '^(?![0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$)(?!dashboard$|settings$|things$|properties$|relations$|messages$)[A-Za-z0-9\-\._~%]+$')
    ->name('things.show-by-slug');

Route::get('/{uuid}', [ItemController::class, 'show'])
    ->whereUuid('uuid')
    ->name('items.show');
