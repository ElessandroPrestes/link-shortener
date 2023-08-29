<?php

namespace App\Tasks;

use App\Models\ShortLink;

class ResetAccessCountsTask
{
    public function __invoke()
    {
        ShortLink::query()->update(['access_count' => 0]);
    }
}