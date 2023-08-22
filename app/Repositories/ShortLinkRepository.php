<?php

namespace App\Repositories;

use App\Interfaces\Repositories\ShortLinkInterface;
use App\Models\ShortLink;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        $query = $this->modelShortLink->orderBy('created_at', 'desc')
                                      ->get();

        $this->cacheService->put($cacheKey, $query, now()->addMinutes(10));

        return $query;
    }

    public function createLink(array $data)
    {
        return $this->modelShortLink->create($data);
    }

    public function searchText(string $link)
    {
        $cacheKey = 'short-link:' . $link;

        if ($this->cacheService->has($cacheKey)) {
            return $this->cacheService->get($cacheKey);
        }

        try {
             $query =  $this->modelShortLink->where('original_url', 'LIKE', "%$link%")
                                            ->orWhere('identifier', 'LIKE', "%$link%")
                                            ->first();
             if (!$query)
                {
                    throw new NotFoundHttpException('Short Link Not Found');
                }

            $this->cacheService->put($cacheKey, $query, now()->addMinutes(10));

            return $query;
            
        } catch (\Throwable $th) {
            throw new NotFoundHttpException('Short Link Not Found', $th);
        }
        
    }

    public function getLinkById(int $id)
    {
        $cacheKey = 'short-link:' . $id;

        if ($this->cacheService->has($cacheKey)) {
            return $this->cacheService->get($cacheKey);
        }

        try {

            $query = $this->modelShortLink->where('id', $id)->firstOrFail();

            return $query;

        } catch (\Throwable $th) {

            throw new NotFoundHttpException('Short Link Not Found');
        }

    }

    public function updateLink(string $link, array $data)
    {
        $shortLink = $this->getLinkById($link);

        $shortLink->update($data);

        return $shortLink;
    }
}