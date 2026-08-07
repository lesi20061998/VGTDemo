<?php

namespace Database\Seeders;

use App\Models\FeaturePack;
use Illuminate\Database\Seeder;

class FeaturePackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $featurePacks = config('feature_packs.groups', []);

        foreach ($featurePacks as $groupKey => $group) {
            foreach ($group['features'] as $featureCode => $featureData) {
                $name = is_array($featureData) ? $featureData['name'] : $featureData;
                $description = is_array($featureData) ? ($featureData['description'] ?? null) : null;

                FeaturePack::updateOrCreate(
                    ['code' => $featureCode],
                    [
                        'name' => $name,
                        'group_name' => $group['label'],
                        'description' => $description,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
