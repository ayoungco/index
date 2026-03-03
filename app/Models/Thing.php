<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class Thing extends Model
{
    /** @use HasFactory<\Database\Factories\ThingFactory> */
    use HasFactory;

    protected $fillable = [
        'slug',
    ];

    /**
     * Get the properties for the thing.
     */
    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    /**
     * Get the outgoing relations for the thing.
     */
    public function outgoingRelations()
    {
        return $this->hasMany(Relation::class, 'from_thing_id');
    }

    /**
     * Get the incoming relations for the thing.
     */
    public function incomingRelations()
    {
        return $this->hasMany(Relation::class, 'to_thing_id');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->slug ?: 'Unnamed Thing';
    }

    public function getCanonicalUrlAttribute(): string
    {
        return url('/'.ltrim((string) $this->slug, '/'));
    }

    /**
     * Build a unified timeline for this thing.
     *
     * @param  array<int, array<string, mixed>>  $sessionEntries
     */
    public function timeline(array $sessionEntries = []): Collection
    {
        $entries = collect([
            [
                'type' => 'thing.created',
                'title' => 'Thing created',
                'description' => 'The Thing record was created in index.',
                'occurred_at' => $this->created_at?->toIso8601String(),
            ],
        ]);

        if ($this->updated_at && ! $this->updated_at->equalTo($this->created_at)) {
            $entries->push([
                'type' => 'thing.updated',
                'title' => 'Thing updated',
                'description' => 'Basic Thing properties were updated.',
                'occurred_at' => $this->updated_at->toIso8601String(),
            ]);
        }

        foreach ($sessionEntries as $entry) {
            $occurredAt = $entry['occurred_at'] ?? null;

            if (! is_string($occurredAt) || trim($occurredAt) === '') {
                continue;
            }

            $entries->push([
                'type' => $entry['type'] ?? 'session.event',
                'title' => $entry['title'] ?? 'Session event',
                'description' => $entry['description'] ?? null,
                'occurred_at' => $occurredAt,
                'photo_url' => $entry['photo_url'] ?? null,
            ]);
        }

        return $entries
            ->filter(fn (array $entry) => isset($entry['occurred_at']))
            ->sortByDesc(function (array $entry) {
                return Carbon::parse($entry['occurred_at'])->getTimestamp();
            })
            ->values();
    }
}
