<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $items = $this->searchItems($search);
        $canCreateFromPhoto = (bool) $request->user()?->email_verified_at;

        return view('dashboard', compact('items', 'search', 'canCreateFromPhoto'));
    }

    private function searchItems(string $search): Collection
    {
        return Item::query()
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
            ->get(['id', 'uuid', 'name', 'slug', 'wikidata_qid', 'type_namespace', 'description', 'created_at']);
    }
}
