<?php

namespace App\Repositories;

use App\Interfaces\ShortLinkInterface;
use App\Models\ShortLink;

class ShortLinkRepository implements ShortLinkInterface
{
    public function createLink(array $data)
    {
        return ShortLink::create($data);
    }
}