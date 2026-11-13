<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cluster extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'category_id'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function contents(): HasMany
    {
        return $this->hasMany(Content::class);
    }

    public function pillar(): HasMany
    {
        return $this->hasMany(Content::class)->where('is_pillar', true);
    }

    public function relatedClusters(): BelongsToMany
    {
        return $this->belongsToMany(Cluster::class, 'cluster_related_cluster', 'cluster_id', 'related_cluster_id');
    }
}
