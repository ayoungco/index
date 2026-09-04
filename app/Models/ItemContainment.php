<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemContainment extends Model
{
    use HasFactory;

    protected $fillable = [
        'container_item_id',
        'contained_item_id',
        'evidence_event_id',
        'created_by',
        'quantity',
        'unit',
        'position',
        'observed_at',
        'removed_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'observed_at' => 'datetime',
            'removed_at' => 'datetime',
        ];
    }

    public function container(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'container_item_id');
    }

    public function containedItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'contained_item_id');
    }

    public function evidenceEvent(): BelongsTo
    {
        return $this->belongsTo(ItemEvent::class, 'evidence_event_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isActive(): bool
    {
        return $this->removed_at === null;
    }
}
