<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchesSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                'name' => [
                    'en' => 'Head Office',
                    'vi' => 'Trụ sở chính',
                ],
                'address' => '123 Main Street, City, Country',
                'map_embed' => '<iframe src="https://www.google.com/maps/embed?pb=..." width="600" height="450"></iframe>',
            ],
            [
                'name' => [
                    'en' => 'Branch 1',
                    'vi' => 'Chi nhánh 1',
                ],
                'address' => '456 Second Street, City, Country',
                'map_embed' => null,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
