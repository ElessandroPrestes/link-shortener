<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;

class UserTest extends BaseModelTestCase
{
    protected function model(): Model
    {
        return new User();
    }

    protected function fillable(): array
    {
        return [
            'name',
            'email',
            'password'
        ];
    }
}
