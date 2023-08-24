<?php

namespace App\Observers;

use App\Models\AccessLog;
use Illuminate\Support\Str;
use App\Models\ShortLink;
use Illuminate\Support\Facades\Cache;
use App\Services\AccessLogService;
use Illuminate\Support\Facades\Log;

class ShortLinkObserver
{
    /**
     * Handle the ShortLink "created" event.
     */
    public function creating(ShortLink $shortLink): void
    {
        // Garante que o identificador Único seja atribuido a cada link encurtado
        if (!$shortLink->short_code)
         {
            $shortLink->short_code = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, rand(6, 8));
         }  
    }


    /**
     * Handle the ShortLink "updated" event.
     */
    public function updated(ShortLink $shortLink): void
    {
        //
    }

    /**
     * Handle the ShortLink "deleted" event.
     */
    public function deleted(ShortLink $shortLink): void
    {
        //
    }

    /**
     * Handle the ShortLink "restored" event.
     */
    public function restored(ShortLink $shortLink): void
    {
        //
    }

    /**
     * Handle the ShortLink "force deleted" event.
     */
    public function forceDeleted(ShortLink $shortLink): void
    {
        //
    }
}
