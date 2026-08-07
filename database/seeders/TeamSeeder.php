<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = [
            [
                'name' => 'Tim Sosmed',
                'label' => 'SMD'
            ],
            [
                'name' => 'Tim RnD',
                'label' => 'RND'
            ]
        ];

        foreach ($teams as $team) {
            Team::create([
                'team_name' => $team['name'],
                'team_slug' => Str::slug($team['name']),
                'team_label' => $team['label']
            ]);
        }
    }
}
