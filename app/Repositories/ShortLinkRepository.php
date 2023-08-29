<?php

namespace App\Repositories;

use App\Interfaces\Repositories\ShortLinkRepositoryInterface;
use App\Models\AccessLog;
use App\Models\ShortLink;
use App\Services\CacheService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class ShortLinkRepository implements ShortLinkRepositoryInterface
{
    protected $modelShortLink;

    protected $cacheService;

    protected $accessLogRepository;

    public function __construct(ShortLink $shortLink, CacheService $cache,  AccessLogRepository $accessLogRepository)
    {
        $this->modelShortLink = $shortLink;

        $this->cacheService = $cache;

        $this->accessLogRepository = $accessLogRepository;
    }

    public function getAllLinks()
    {
        $cacheKey = 'short-links:all';

        if ($this->cacheService->has($cacheKey)) {
            return $this->cacheService->get($cacheKey);
        }

        $query = $this->modelShortLink->with('accessLogs')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($query->isEmpty()) {
            throw new NotFoundHttpException('No Short Links found');
        }

        $this->cacheService->put($cacheKey, $query);

        return $query;
    }

    public function createLink(array $data)
    {
        $data['short_code'] = $this->handleShortCode($data['short_code']);

        $shortLink =  $this->modelShortLink->create($data);

        $this->accessLogRepository->createAccessLog([
            'short_link_id' => $shortLink->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return $shortLink;
    }

    public function getLinkById(int $id)
    {
        $cacheKey = 'short-link:' . $id;

        if ($this->cacheService->has($cacheKey)) {
            return $this->cacheService->get($cacheKey);
        }

        try {

            $query = $this->modelShortLink->with('accessLogs')
                ->where('id', $id)
                ->findOrFail($id);

            $this->cacheService->put($cacheKey, $query);

            return $query;
        } catch (ModelNotFoundException $th) {

            throw new NotFoundHttpException('Short Link Not Found');
        }
    }

    public function updateLink(int $id, array $data)
    {
        $shortLink = $this->getLinkById($id);

       if (isset($data['short_code']))
        {
            $data['short_code'] = $this->handleShortCode($data['short_code']);
        }

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

    public function searchCode(string $slug)
    {
        $cacheKey = 'short_code:' . $slug;

        if ($this->cacheService->has($cacheKey)) {
            return $this->cacheService->get($cacheKey);
        }
        $query = $this->modelShortLink
            ->with('accessLogs')
            ->where(function (Builder $query) use ($slug) {
                $query->where('original_url', 'LIKE', "%$slug%")
                ->orWhere('short_code', 'LIKE', "%$slug%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

        if ($query->isEmpty()) {
            throw new NotFoundHttpException('Short Code Not Found');
        }

        $this->cacheService->put($cacheKey, $query);

        return $query;
    }

    public function handleShortCode(?string $code)
    {
        if ($code !==null) {
            return $code; 
        }
        return Str::random(rand(6, 8));
    }

    public function incrementAccessCount(int $id)
    {
        $shortCode = $this->getLinkById($id);
       // dd($shortCode);

        if ($shortCode) {
            $shortCode->increment('access_count');
        }

    }
}
