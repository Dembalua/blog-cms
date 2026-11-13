<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AffiliateLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliate_network_id',
        'name',
        'slug',
        'url',
        'is_active',
        'clicks',
        'last_checked_at',
        'last_status_code',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_checked_at' => 'datetime',
    ];

    public function network(): BelongsTo
    {
        return $this->belongsTo(AffiliateNetwork::class, 'affiliate_network_id');
    }

    public function clicksPerContent(): HasMany
    {
        return $this->hasMany(AffiliateLinkClick::class);
    }
}
