<?php

namespace App\Services;

use App\Exceptions\ShortLinkNotFoundException;
use App\Interfaces\Repositories\ShortLinkRepositoryInterface;
use App\Interfaces\Services\CacheServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ShortLinkService
{
    public function __construct(
        protected ShortLinkRepositoryInterface $shortLinkRepository,
        protected CacheServiceInterface $cacheService)
    {
        $this->shortLinkRepository = $shortLinkRepository;
        $this->cacheService = $cacheService;
    }

    public function indexLinks()
    {
        $cacheKey = 'short-links:all';

        if ($this->cacheService->has($cacheKey)) {
            return $this->cacheService->get($cacheKey);
        }
        
        $result = $this->shortLinkRepository->getAllLinks();
        $this->cacheService->put($cacheKey, $result, null);
        
        return $result;

    }

    public function storeLink(array $data)
    {
        $this->cacheService->forget('short-links:all');

        return $this->shortLinkRepository->createLink($data);
    }

    public function validatePositiveIntegerId($id)
    {
        if (!ctype_digit($id) || intval($id) <= 0) {
            throw new BadRequestHttpException('ID must be a positive integer',  null, Response::HTTP_BAD_REQUEST);
        }
    }

    public function showLink($id)
    {
        try {
            $this->validatePositiveIntegerId($id);

            $cacheKey = 'short-link:' . $id;
        
            if ($this->cacheService->has($cacheKey)) {
                return $this->cacheService->get($cacheKey);
            } else {
                $link = $this->shortLinkRepository->getLinkById($id);
                if (!$link) {
                    throw new ShortLinkNotFoundException();
                }
                $this->cacheService->put($cacheKey, $link, null);
                return $link;
            }


        } catch (ModelNotFoundException $e) {
            throw new ShortLinkNotFoundException();
        }
    }

    public function updateLink(int $id, array $data)
    {
        $this->cacheService->forget('short-links:all');

        $this->cacheService->forget("short-link:$id");

        if ($this->shortLinkRepository->getLinkById($id)) {
            if (!isset($data['short_code'])) {
                $data['short_code'] = Str::random(rand(6, 8));
            }

            return $this->shortLinkRepository->updateLink($id, $data);
        } else {
            throw new ShortLinkNotFoundException();
        }
    }

    public function destroyLink(int $id)
    {
        $this->cacheService->forget('short-links:all');

        $this->cacheService->forget("short-link:$id");

        return $this->shortLinkRepository->deleteLink($id);
    }

    public function searchCode(string $slug)
    {
        $cacheKey = 'short-code:' . $slug;
    
        if ($this->cacheService->has($cacheKey)) {
            return $this->cacheService->get($cacheKey);
        }

        $shortCode = $this->shortLinkRepository->searchCode($slug);

        if ($shortCode->isEmpty()) {
            throw new ShortLinkNotFoundException();
        }

        $this->cacheService->put($cacheKey, $shortCode, null);

        return $shortCode;
    }
  
}
