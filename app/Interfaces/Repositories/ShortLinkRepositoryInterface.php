<?php

namespace App\Interfaces\Repositories;


interface ShortLinkRepositoryInterface
{
    public function getAllLinks();
    
    public function createLink(array $data);

    public function searchCode(string $shortCode);

    public function getLinkById(int $id);

    public function updateLink(int $id, array $data);

    public function deleteLink(int $id);
}
