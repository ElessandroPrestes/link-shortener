<?php

namespace App\Repositories;

use App\Interfaces\Repositories\ShortLinkInterface;
use App\Models\ShortLink;
use App\Services\CacheService;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class ShortLinkRepository implements ShortLinkInterface
{
    protected $modelShortLink;
    
    protected $cacheService;

    public function __construct(ShortLink $shortLink, CacheService $cache)
    {
        $this->modelShortLink = $shortLink;

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
        
        if ($query->isEmpty()) {
            throw new NotFoundHttpException('No Short Links found');
        }

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
                                            ->orderBy('created_at', 'desc')
                                            ->get();
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

            $query = $this->modelShortLink->findOrFail($id);
            
            return $query;

        } catch (ModelNotFoundException $th) {

            throw new NotFoundHttpException('Short Link Not Found');
        }

    }

    public function updateLink(int $id, array $data)
    {
        $shortLink = $this->getLinkById($id);
    
        $shortLink->update($data);
        
        return $shortLink;
    }

    public function deleteLink(int $id)
    {
        try {
            $shortLink = $this->getLinkById($id);
    
            $this->cacheService->forget('short-link:' . $id);

            $this->cacheService->forget('short-links:all');
    
            $deleted = $shortLink->delete();

            if (!$deleted) {
                throw new \Exception('Failed to delete Short Link');
            }
            
            return true;

        } catch (\Throwable $th) {

            throw new NotFoundHttpException('Short Link not found');
        }
    }
}