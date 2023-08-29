<?php

namespace App\Services;

use App\Repositories\ShortLinkRepository;
use Illuminate\Support\Str;
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
        return $this->shortLinkRepository->createLink($data);
    }

    public function searchCode(string $slug)
    {
        try {
            return $this->shortLinkRepository->searchCode($slug);
        } catch (\Throwable $th) {
            throw new NotFoundHttpException('Short Code Not Found');
        }
    }

    public function updateLink(int $id, array $data)
    {
        if ($this->shortLinkRepository->getLinkById($id)) {
            if (!isset($data['short_code'])) {
                $data['short_code'] = Str::random(rand(6, 8));
            }

            return $this->shortLinkRepository->updateLink($id, $data);
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
