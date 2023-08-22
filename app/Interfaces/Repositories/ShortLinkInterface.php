<?php

namespace App\Interfaces\Repositories;

use Illuminate\Support\Collection;

interface ShortLinkInterface
{
    public function getAllLinks();
    
    public function createLink(array $data);

    public function searchText(string $link);

    public function getLinkById(int $id);

    public function updateLink(string $link, array $data);
}
