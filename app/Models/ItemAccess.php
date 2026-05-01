<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemAccess extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'user_id',
        'ip_address',
        'user_agent',
        'browser',
        'city',
        'country',
        'country_code',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actorLabel(): string
    {
        if ($this->user) {
            return $this->user->name;
        }

        $location = trim(implode(', ', array_filter([
            $this->city ?: 'unknown city',
            $this->country ?: $this->country_code ?: 'unknown country',
        ])));

        return sprintf(
            'anonymous user from %s using %s',
            $location,
            $this->browser ?: 'unknown browser',
        );
    }

    public function countryFlag(): ?string
    {
        $code = strtoupper((string) $this->country_code);

        if (! preg_match('/^[A-Z]{2}$/', $code)) {
            return null;
        }

        return mb_chr(127397 + ord($code[0]), 'UTF-8').mb_chr(127397 + ord($code[1]), 'UTF-8');
    }
}
