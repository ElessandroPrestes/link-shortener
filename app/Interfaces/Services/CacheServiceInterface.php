<?php

namespace App\Interfaces\Services;

use Illuminate\Support\Facades\Cache;

interface CacheServiceInterface
{
    public function rememberForever(string $key, $value);
    public function get(string $key);
    public function put(string $key, $value, $ttl);
    public function forget(string $key);
}

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
