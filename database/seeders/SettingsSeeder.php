<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'site_name',
                'payload' => ['value' => 'My Website'],
                'group' => 'general',
                'locked' => false,
            ],
            [
                'key' => 'site_logo',
                'payload' => ['url' => '/images/logo.png'],
                'group' => 'general',
                'locked' => false,
            ],
            [
                'key' => 'header_style',
                'payload' => ['template' => 'default'],
                'group' => 'theme',
                'locked' => false,
            ],
            [
                'key' => 'footer_style',
                'payload' => ['template' => 'default'],
                'group' => 'theme',
                'locked' => false,
            ],
            [
                'key' => 'primary_color',
                'payload' => ['color' => '#3490dc'],
                'group' => 'theme',
                'locked' => false,
            ],
            [
                'key' => 'secondary_color',
                'payload' => ['color' => '#ffed4e'],
                'group' => 'theme',
                'locked' => false,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
