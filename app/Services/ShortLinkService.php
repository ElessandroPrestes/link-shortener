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

    public function storeLink(array $data)
    {
        if (!isset($data['identifier'])) {
            $data['identifier'] = Str::random(rand(6, 8));
        }

        return $this->shortLinkRepository->createLink($data);
    }

}