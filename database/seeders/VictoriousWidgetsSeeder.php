<?php

namespace Database\Seeders;

use App\Models\Widget;
use Illuminate\Database\Seeder;

class VictoriousWidgetsSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = session('current_project')['id'] ?? 1;
        $area = 'homepage-main';
        
        // Clear existing widgets for this area
        Widget::where('area', $area)->where('tenant_id', $tenantId)->delete();
        
        $widgets = [
            // 1. Hero Video
            [
                'name' => 'Hero Video',
                'type' => 'victorious_hero_video',
                'area' => $area,
                'sort_order' => 1,
                'is_active' => true,
                'settings' => [
                    'video_url' => asset('themes/victorious/video/video-duthuyen.mp4'),
                    'poster_image' => '',
                    'overlay_opacity' => 0,
                    'height' => '100vh',
                ],
            ],
            
            // 2. About Us
            [
                'name' => 'About Us',
                'type' => 'victorious_about',
                'area' => $area,
                'sort_order' => 2,
                'is_active' => true,
                'settings' => [
                    'section_title' => 'ABOUT US',
                    'image' => asset('themes/victorious/img/common/about-img-1.png'),
                    'decor_image' => asset('themes/victorious/img/common/decor-about.svg'),
                    'title' => 'CRUISE WHERE YOU FEEL MOST ALIVE',
                    'content' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.',
                    'background_image' => asset('themes/victorious/img/common/about-services-bg.png'),
                ],
            ],
            
            // 3. Services Icons
            [
                'name' => 'Activities and Services',
                'type' => 'victorious_services',
                'area' => $area,
                'sort_order' => 3,
                'is_active' => true,
                'settings' => [
                    'title' => 'ACTIVITIES AND SERVICES',
                    'services' => [
                        ['icon' => asset('themes/victorious/img/icon/service-item-1.svg'), 'name' => 'Aquarius Pool'],
                        ['icon' => asset('themes/victorious/img/icon/service-item-2.svg'), 'name' => 'Venus Spa'],
                        ['icon' => asset('themes/victorious/img/icon/service-item-3.svg'), 'name' => 'Capella Restaurant'],
                        ['icon' => asset('themes/victorious/img/icon/service-item-4.svg'), 'name' => 'Sundeck'],
                        ['icon' => asset('themes/victorious/img/icon/service-item-5.svg'), 'name' => 'Carina Bar'],
                        ['icon' => asset('themes/victorious/img/icon/service-item-6.svg'), 'name' => 'Gemini Gym'],
                        ['icon' => asset('themes/victorious/img/icon/service-item-7.svg'), 'name' => 'Events'],
                    ],
                ],
            ],
            
            // 4. Service Detail (Spa)
            [
                'name' => 'Ocean Luxe Spa',
                'type' => 'victorious_service_detail',
                'area' => $area,
                'sort_order' => 4,
                'is_active' => true,
                'settings' => [
                    'image' => asset('themes/victorious/img/common/service-banner-1.png'),
                    'decor_image' => asset('themes/victorious/img/common/decor-about.svg'),
                    'title' => 'Ocean Luxe Spa',
                    'content' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.',
                    'button_text' => 'BOOK ROOM',
                    'button_link' => '/booking',
                    'layout' => 'image-left',
                ],
            ],
            
            // 5. Itineraries
            [
                'name' => 'Our Itineraries',
                'type' => 'victorious_itineraries',
                'area' => $area,
                'sort_order' => 5,
                'is_active' => true,
                'settings' => [
                    'title' => 'OUR ITINERARIES',
                    'itineraries' => [
                        ['image' => asset('themes/victorious/img/common/night-1.png'), 'duration' => '2 DAYS / 1 NIGHT', 'link' => '/itinerary/2-days-1-night'],
                        ['image' => asset('themes/victorious/img/common/nights-2.png'), 'duration' => '3 DAYS / 2 NIGHTS', 'link' => '/itinerary/3-days-2-nights'],
                    ],
                    'button_text' => 'VIEW MORE',
                ],
            ],
            
            // 6. Room Categories
            [
                'name' => 'Room Categories',
                'type' => 'victorious_room_categories',
                'area' => $area,
                'sort_order' => 6,
                'is_active' => true,
                'settings' => [
                    'title' => 'ROOM CATEGORIES',
                    'rooms' => [], // Will be populated from Products
                    'show_features' => true,
                    'view_more_text' => 'VIEW MORE',
                    'book_text' => 'BOOK ROOM',
                ],
            ],
            
            // 7. Special Offers
            [
                'name' => 'Special Offers',
                'type' => 'victorious_special_offers',
                'area' => $area,
                'sort_order' => 7,
                'is_active' => true,
                'settings' => [
                    'title' => 'SPECIAL OFFERS',
                    'view_all_link' => '/offers',
                    'offers_large' => [
                        ['image' => asset('themes/victorious/img/common/special-offers-1.png'), 'title' => 'EXPERIENCE PRESIDENTIAL-CLASS PACKAGE', 'link' => '/offer/presidential'],
                        ['image' => asset('themes/victorious/img/common/special-offers-2.png'), 'title' => 'HONEY MOON PACKAGE', 'link' => '/offer/honeymoon'],
                    ],
                    'offers_small' => [
                        ['image' => asset('themes/victorious/img/common/special-offers-3.png'), 'title' => 'AUTUMN SYMPHONY PROMOTION', 'link' => '/offer/autumn'],
                        ['image' => asset('themes/victorious/img/common/special-offers-4.png'), 'title' => 'BIRTHDAY PACKAGE', 'link' => '/offer/birthday'],
                        ['image' => asset('themes/victorious/img/common/special-offers-5.png'), 'title' => 'MICE PACKAGE', 'link' => '/offer/mice'],
                    ],
                ],
            ],
            
            // 8. Events
            [
                'name' => 'Events',
                'type' => 'victorious_events',
                'area' => $area,
                'sort_order' => 8,
                'is_active' => true,
                'settings' => [
                    'title' => 'EVENTS',
                    'posts' => [], // Will be populated from Posts
                    'limit' => 3,
                    'columns' => '3',
                ],
            ],
        ];
        
        foreach ($widgets as $widgetData) {
            $widgetData['tenant_id'] = $tenantId;
            Widget::create($widgetData);
        }
        
        $this->command->info('Victorious widgets seeded successfully!');
    }
}
