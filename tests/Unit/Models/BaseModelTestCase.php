<?php

namespace Tests\Unit\Models;

use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;

abstract class BaseModelTestCase extends TestCase
{
    abstract protected function model(): Model;
    
    abstract protected function fillable(): array;
   
    /**
     * @test
     */

    public function has_fillable()
    {
        $fillable = $this->model()->getFillable();

        $this->assertEquals($this->fillable(), $fillable);
    }
}
