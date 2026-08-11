<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class ThemeSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'theme_primary_color' => '#98191F',
            'theme_secondary_color' => '#1F2937',
            'theme_text_color' => '#374151',
            'theme_bg_color' => '#FFFFFF',
            'theme_font_family' => "'Poppins', sans-serif",
            'theme_heading_font_family' => "'Montserrat', sans-serif",
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value, 'theme');
        }
    }
}
