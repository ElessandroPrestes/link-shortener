<?php

namespace App\Interfaces\Repositories;

interface AccessLogRepositoryInterface
{
    public function createAccessLog(array $data);
}