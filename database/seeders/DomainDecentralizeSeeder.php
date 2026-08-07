<?php

namespace Database\Seeders;

use App\Models\DomainDecentralize;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DomainDecentralizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $domains = ['https://yourdomain.com', 'https://bisacuan.xyz', 'https://numpangbentar.online'];
        foreach ($domains as $domain) {
            DomainDecentralize::create([
                'domain_url' => $domain,
                'api_key' => Str::random(12)
            ]);
        }
    }
}
