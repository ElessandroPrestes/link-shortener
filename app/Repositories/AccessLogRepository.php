<?php

namespace App\Repositories;

use App\Models\AccessLog;
use App\Interfaces\Repositories\AccessLogRepositoryInterface;

class AccessLogRepository implements AccessLogRepositoryInterface
{
    protected $modelAccessLog;

    public function __construct(AccessLog $accessLog)
    {
        $this->modelAccessLog = $accessLog;
    }

    public function createAccessLog(array $data)
    {
        return $this->modelAccessLog->create($data);
    }
}