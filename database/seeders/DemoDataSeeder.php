<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        $project = Project::first();
        $tenantId = session('current_tenant_id') ?? ($project ? $project->id : null);
        $projectId = session('current_project_id') ?? ($project ? $project->id : null);

        // Posts
        $posts = [
            ['title' => 'Getting Started with Modern Web Development', 'slug' => 'getting-started-web-dev', 'content' => '<p>Learn the fundamentals of modern web development with our comprehensive guide covering HTML, CSS, JavaScript, and popular frameworks.</p>'],
            ['title' => '10 Tips for Better Code Quality', 'slug' => '10-tips-code-quality', 'content' => '<p>Discover proven strategies to write cleaner, more maintainable code that your team will love working with.</p>'],
            ['title' => 'The Future of AI in Business', 'slug' => 'future-ai-business', 'content' => '<p>Explore how artificial intelligence is transforming industries and creating new opportunities for innovation.</p>'],
            ['title' => 'Building Scalable Applications', 'slug' => 'building-scalable-apps', 'content' => '<p>Best practices and architectural patterns for building applications that can grow with your business.</p>'],
            ['title' => 'Cybersecurity Best Practices', 'slug' => 'cybersecurity-best-practices', 'content' => '<p>Essential security measures every developer should implement to protect user data and prevent breaches.</p>'],
            ['title' => 'Cloud Computing Essentials', 'slug' => 'cloud-computing-essentials', 'content' => '<p>Understanding cloud infrastructure and how to leverage it for maximum efficiency and cost savings.</p>'],
        ];

        foreach ($posts as $post) {
            DB::table('posts')->insertOrIgnore([
                'title' => $post['title'],
                'slug' => $post['slug'],
                'content' => $post['content'],
                'post_type' => 'post',
                'status' => 'published',
                'tenant_id' => $tenantId,
                'project_id' => $projectId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Feature Pack: Real Estate
        $properties = [
            ['title' => 'Biệt thự biển Vũng Tàu', 'slug' => 'biet-thu-bien-vung-tau', 'content' => '<p>Biệt thự nghỉ dưỡng cao cấp view biển trực diện với 5 phòng ngủ, hồ bơi riêng.</p>'],
            ['title' => 'Căn hộ Landmark 81', 'slug' => 'can-ho-landmark-81', 'content' => '<p>Căn hộ 3 phòng ngủ tầng cao view sông Sài Gòn tuyệt đẹp, full nội thất cao cấp.</p>'],
        ];

        foreach ($properties as $property) {
            DB::table('posts')->insertOrIgnore([
                'title' => $property['title'],
                'slug' => $property['slug'],
                'content' => $property['content'],
                'post_type' => 'property',
                'status' => 'published',
                'tenant_id' => $tenantId,
                'project_id' => $projectId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Feature Pack: Medical
        $doctors = [
            ['title' => 'BS. Nguyễn Văn A', 'slug' => 'bs-nguyen-van-a', 'content' => '<p>Chuyên khoa Tim Mạch, 15 năm kinh nghiệm công tác tại bệnh viện tuyến trung ương.</p>'],
            ['title' => 'BS. Trần Thị B', 'slug' => 'bs-tran-thi-b', 'content' => '<p>Chuyên khoa Nhi, Nguyên Trưởng khoa Bệnh viện X.</p>'],
        ];

        foreach ($doctors as $doctor) {
            DB::table('posts')->insertOrIgnore([
                'title' => $doctor['title'],
                'slug' => $doctor['slug'],
                'content' => $doctor['content'],
                'post_type' => 'doctor',
                'status' => 'published',
                'tenant_id' => $tenantId,
                'project_id' => $projectId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Feature Pack: Hotel
        $rooms = [
            ['title' => 'Phòng Deluxe Hướng Biển', 'slug' => 'deluxe-ocean-view', 'content' => '<p>Phòng rộng 40m2 với ban công nhìn ra biển, trang bị giường King size, bồn tắm nằm.</p>'],
            ['title' => 'Phòng Suite Gia Đình', 'slug' => 'family-suite', 'content' => '<p>Phòng suite 2 phòng ngủ thích hợp cho gia đình 4 người, có không gian phòng khách riêng biệt.</p>'],
        ];

        foreach ($rooms as $room) {
            DB::table('posts')->insertOrIgnore([
                'title' => $room['title'],
                'slug' => $room['slug'],
                'content' => $room['content'],
                'post_type' => 'room',
                'status' => 'published',
                'tenant_id' => $tenantId,
                'project_id' => $projectId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
