<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'wikidata_qid',
        'type_namespace',
        'description',
        'user_id',
    ];

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
}
