<?php

namespace Tests\Unit\Models;

use App\Models\AccesLog;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;

class AceesLogTest extends BaseModelTestCase
{

    protected function model(): Model
    {
        return new AccesLog();
    }
    protected function fillable(): array
    {
        return [
            'short_link_id',
            'ip_address',
            'user_agent'
        ];
    }
}
