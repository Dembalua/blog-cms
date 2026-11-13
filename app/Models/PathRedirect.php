<?php

namespace App\Models;

use App\Enums\RedirectStatus;
use Illuminate\Database\Eloquent\Model;

class PathRedirect extends Model
{
    protected $fillable = [
        'old_path',
        'new_path',
        'status_code',
    ];

    protected $casts = [
        'status_code' => RedirectStatus::class,
    ];
}
