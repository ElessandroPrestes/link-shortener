<?php

namespace App\Repositories;

use App\Exceptions\ShortLinkNotFoundException;
use App\Interfaces\Repositories\AccessLogRepositoryInterface;
use App\Interfaces\Repositories\ShortLinkRepositoryInterface;
use App\Models\ShortLink;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ShortLinkRepository implements ShortLinkRepositoryInterface
{
   
    public function __construct(
        protected ShortLink $modelShortLink,
        protected AccessLogRepositoryInterface $accessLogRepository)
    {
        $this->modelShortLink = $modelShortLink;
        $this->accessLogRepository = $accessLogRepository;
    }

    public function getAllLinks()
    {
        $query =  $this->modelShortLink->with('accessLogs')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($query->isEmpty()) {
            throw new ShortLinkNotFoundException('No Short Links found');
        }

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
        $query = $this->modelShortLink->with('accessLogs')->find($id);

        if (!$query) {
            throw new ShortLinkNotFoundException();
        }

        return $query;
       
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
        $shortLink = $this->getLinkById($id);

        $deleted = $shortLink->delete();

        if (!$deleted) {
            throw new ShortLinkNotFoundException();
        }

        return true;
       
    }

    public function searchCode(string $slug)
    {
        
        $query = $this->modelShortLink
            ->with('accessLogs')
            ->where(function (Builder $query) use ($slug) {
                $query->where('original_url', 'LIKE', "%$slug%")
                ->orWhere('short_code', 'LIKE', "%$slug%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

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
      
        if ($shortCode) {
            $shortCode->increment('access_count');
        }

    }

    public function validatePositiveIntegerId($id)
    {
        if (!ctype_digit($id) || intval($id) <= 0) {
            throw new BadRequestHttpException('ID must be a positive integer', null, Response::HTTP_BAD_REQUEST);
        }
    }

    
}
