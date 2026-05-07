<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'user_id',
        'image_path',
        'comment',
        'tags',
        'is_qr_verified',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_qr_verified' => 'boolean',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function author(): BelongsTo
    {
        return $this->user();
    }
}
