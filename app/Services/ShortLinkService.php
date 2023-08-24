<?php

namespace App\Services;

use App\Jobs\CreateShortLinkJob;
use App\Jobs\UpdateShortLinkJob;
use Illuminate\Support\Facades\Bus;
use App\Repositories\ShortLinkRepository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ShortLinkService
{
    protected $shortLinkRepository;

    public function __construct(ShortLinkRepository $shortLinkRepository)
    {
        $this->shortLinkRepository = $shortLinkRepository;
    }

    public function indexLinks()
    {
        return $this->shortLinkRepository->getAllLinks();
    }

    public function storeLink(array $data)
    {
        if (!isset($data['short_code'])) {
            $data['short_code'] = Str::random(rand(6, 8));
        }

        return Bus::dispatch(new CreateShortLinkJob($data));

    }

    public function searchCode(string $shortCode)
    {
        return $this->shortLinkRepository->searchText($shortCode);
    }

    public function updateLink(int $id, array $data)
    {
        
        if ($this->shortLinkRepository->getLinkById($id))
         {
            if (!isset($data['short_code']))
             {
                $data['short_code'] = Str::random(rand(6, 8));
            }
        
            Bus::dispatch(new UpdateShortLinkJob($id, $data));
        
        } else {
            throw new NotFoundHttpException('Short Link Not Found');
        }
    }

    public function showLink(int $id)
    {
        return $this->shortLinkRepository->getLinkById($id);
    }


    public function destroyLink(int $id)
    {
        return $this->shortLinkRepository->deleteLink($id);
    }

}