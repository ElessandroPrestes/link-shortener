<?php

namespace Database\Seeders;

use App\Models\AccessLog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccessLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AccessLog::factory(10)->create(); 
    }
}
