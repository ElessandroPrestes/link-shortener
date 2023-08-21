<?php

namespace App\Repositories;

use App\Interfaces\Repositories\ShortLinkInterface;
use App\Models\ShortLink;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;

class ShortLinkRepository implements ShortLinkInterface
{
    protected $modelShortLink;
    
    protected $cacheService;

    public function __construct(ShortLink $link, CacheService $cache)
    {
        $this->modelShortLink = $link;

        $this->cacheService = $cache;
    }

    public function getAllLinks()
    {
        $cacheKey = 'short-links:all';

        if ($this->cacheService->get($cacheKey)) {
            return $this->cacheService->get($cacheKey);
        }

        $query = $this->modelShortLink->orderBy('created_at', 'desc')->get();

        $this->cacheService->put($cacheKey, $query, now()->addMinutes(10));

        return $query;
    }

    public function createLink(array $data)
    {
        return $this->modelShortLink->create($data);
    }
}