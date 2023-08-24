<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShortLink extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['original_url', 'short_code'];

    protected $casts = [
        'access_count' => 'integer',
    ];

    public function accessLogs(): HasMany
    {
        return $this->hasMany(AccessLog::class);
    }
}
