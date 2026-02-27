<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'scanned_item_id',
        'user_id',
        'image_path',
        'is_qr_verified',
    ];

    protected $casts = [
        'is_qr_verified' => 'boolean',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'scanned_item_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
