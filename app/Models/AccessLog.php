<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccessLog extends Model
{
    use HasFactory;

    protected $fillable = ['short_link_id', 'ip_address', 'user_agent'];

    public function shortLink(): BelongsTo
    {
        return $this->belongsTo(ShortLink::class);
    }

}
