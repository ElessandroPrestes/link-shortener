<?php

namespace App\Services;

use App\Interfaces\Services\CacheServiceInterface;
use Illuminate\Support\Facades\Cache;

class CacheService implements CacheServiceInterface
{
    public function rememberForever(string $key, $value)
    {
        return Cache::rememberForever($key, function () use ($value) {
            return $value;
        });
    }

    public function get(string $key)
    {
        return Cache::get($key);
    }

    public function put(string $key, $value, $ttl)
    {
        return Cache::put($key, $value, $ttl);
    }

    public function forget(string $key)
    {
        return Cache::forget($key);
    }
}
