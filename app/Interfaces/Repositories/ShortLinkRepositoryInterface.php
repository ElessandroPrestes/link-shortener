<?php

namespace App\Interfaces\Repositories;


interface ShortLinkRepositoryInterface
{
    public function getAllLinks();
    
    public function createLink(array $data);

    public function getLinkById(int $id);

    public function updateLink(int $id, array $data);

    public function deleteLink(int $id);

    public function searchCode(string $slug);

    public function handleShortCode(string $code);

    public function incrementAccessCount(int $id);

}
