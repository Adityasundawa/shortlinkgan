<?php

namespace Database\Seeders;

use App\Models\TemplateMicrosite;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TemplateMicrositeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TemplateMicrosite::create([
            "name" => "Template 1",
            "preview" => "1.png"
        ]);

        TemplateMicrosite::create([
            "name" => "Template 2",
            "preview" => "2.png"
        ]);
    }
}
