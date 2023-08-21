<?php

namespace Database\Seeders;

use App\Models\ShortLink;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShortLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ShortLink::factory(10)->create(); 
    }
}
