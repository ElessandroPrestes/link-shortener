<?php

namespace App\Services;


use App\Repositories\ShortLinkRepository;
use Illuminate\Support\Str;

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
        if (!isset($data['identifier'])) {
            $data['identifier'] = Str::random(rand(6, 8));
        }

        return $this->shortLinkRepository->createLink($data);
    }

    public function searchText(string $link)
    {
        return $this->shortLinkRepository->searchText($link);
    }

    public function updateLink(string $link, array $data)
    {
        return $this->shortLinkRepository->updateLink($link, $data);
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