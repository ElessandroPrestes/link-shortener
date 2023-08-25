<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShortLink extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'original_url', 'short_code', 'expiration_date', 'access_count',];

    protected $casts = [
        'access_count' => 'integer',
    ];

    public function accessLogs(): HasMany
    {
        return $this->hasMany(AccessLog::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function linkValidator($query)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays(30))
                     ->where('expiration_date', '>=', Carbon::now());
    }
}
