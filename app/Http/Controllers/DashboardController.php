<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
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

    public function search(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('q', ''));

        if ($search === '') {
            return response()->json([
                'results' => [],
            ]);
        }

        $results = $this->searchItems($search, 8)
            ->map(fn (Item $item): array => [
                'name' => $item->name,
                'description' => $item->description,
                'type' => $item->typeLabel(),
                'url' => $item->semanticUrl() ?? route('items.show', ['uuid' => $item->uuid]),
            ])
            ->values();

        return response()->json([
            'results' => $results,
        ]);
    }

    private function searchItems(string $search, int $limit = 100): Collection
    {
        return Item::query()
            ->with(['featuredEvent', 'latestPhoto'])
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $likeQuery) use ($search) {
                    $operator = $likeQuery->getConnection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
                    $like = "%{$search}%";

                    $likeQuery
                        ->where('name', $operator, $like)
                        ->orWhere('description', $operator, $like);
                });
            })
            ->latest('created_at')
            ->limit($limit)
            ->get(['id', 'uuid', 'name', 'slug', 'wikidata_qid', 'type_namespace', 'description', 'featured_event_id', 'created_at']);
    }
}
