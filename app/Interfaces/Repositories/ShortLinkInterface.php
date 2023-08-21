<?php

namespace App\Interfaces\Repositories;

interface ShortLinkInterface
{
    public function getAllLinks();
    
    public function createLink(array $data);
}
