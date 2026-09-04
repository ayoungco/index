<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'wikidata_qid',
        'type_namespace',
        'operational_role',
        'description',
        'is_public',
        'user_id',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function isPublic(): bool
    {
        return (bool) $this->is_public;
    }

    public function semanticUrl(): ?string
    {
        if (! $this->type_namespace || ! $this->slug) {
            return null;
        }

        return route('items.semantic.show', [
            'namespace' => $this->type_namespace,
            'slug' => $this->slug,
        ]);
    }

    public function typeLabel(): string
    {
        if (! $this->type_namespace) {
            return 'Unclassified asset';
        }

        return Str::of($this->type_namespace)
            ->replace(['-', '_'], ' ')
            ->headline()
            ->toString();
    }

    public function operationalRoleLabel(): ?string
    {
        return match ($this->operational_role) {
            'product' => 'Product',
            'holding_unit' => 'Holding unit',
            'transportation_unit' => 'Transportation unit',
            'location' => 'Location / bay',
            'asset' => 'Asset',
            'other' => 'Other',
            default => null,
        };
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(ItemEvent::class)->latest();
    }

    public function accesses(): HasMany
    {
        return $this->hasMany(ItemAccess::class)->latest();
    }

    public function containedItems(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'item_containments',
            'container_item_id',
            'contained_item_id',
        )
            ->withPivot([
                'evidence_event_id',
                'created_by',
                'quantity',
                'unit',
                'position',
                'observed_at',
                'removed_at',
            ])
            ->withTimestamps()
            ->wherePivotNull('removed_at');
    }

    public function containers(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'item_containments',
            'contained_item_id',
            'container_item_id',
        )
            ->withPivot([
                'evidence_event_id',
                'created_by',
                'quantity',
                'unit',
                'position',
                'observed_at',
                'removed_at',
            ])
            ->withTimestamps()
            ->wherePivotNull('removed_at');
    }

    public function featuredEvent(): BelongsTo
    {
        return $this->belongsTo(ItemEvent::class, 'featured_event_id');
    }

    public function latestPhoto(): HasOne
    {
        return $this->hasOne(ItemEvent::class)->latestOfMany('created_at');
    }

    public function featuredPhotoPath(): ?string
    {
        return $this->featuredEvent?->image_path ?? $this->latestPhoto?->image_path;
    }
}
