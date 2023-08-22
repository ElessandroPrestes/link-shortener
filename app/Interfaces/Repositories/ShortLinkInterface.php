<?php

namespace App\Interfaces\Repositories;

use Illuminate\Support\Collection;

interface ShortLinkInterface
{
    public function getAllLinks();
    
    public function createLink(array $data);

    public function getLinkByText(string $link);
}
