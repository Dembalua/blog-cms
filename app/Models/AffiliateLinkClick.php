<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateLinkClick extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'affiliate_link_id',
        'content_id',
        'clicks',
        'last_clicked_at',
    ];

    protected $casts = [
        'last_clicked_at' => 'datetime',
    ];

    public function affiliateLink(): BelongsTo
    {
        return $this->belongsTo(AffiliateLink::class);
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }
}
