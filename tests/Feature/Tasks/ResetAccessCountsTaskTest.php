<?php

namespace Tests\Feature\Tasks;

use App\Models\ShortLink;
use App\Tasks\ResetAccessCountsTask;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ResetAccessCountsTaskTest extends TestCase
{
    /**
     * @test
     */

     public function reset_access_counts_task()
     {
        ShortLink::factory()->create(['access_count' => 5]);

        ShortLink::factory()->create(['access_count' => 10]);

        $resetTask = new ResetAccessCountsTask();
        
        $resetTask();

        $this->assertEquals(0, ShortLink::sum('access_count'));
     }
    
}
