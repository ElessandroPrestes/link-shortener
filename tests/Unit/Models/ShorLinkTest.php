<?php

namespace Tests\Unit\Models;

use App\Models\ShortLink;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;

class ShorLinkTest extends BaseModelTestCase
{
    protected function model(): Model
    {
        return new ShortLink();
    }

    protected function fillable(): array
    {
        return [
            'original_url',
            'identifier',
        ];
    }
    
}
