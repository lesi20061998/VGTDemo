<?php

namespace Tests\Feature;

use App\Http\Requests\BrandRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class BrandValidationTest extends TestCase
{
    public function test_name_is_required(): void
    {
        $data = [
            'slug' => 'test-slug',
            'description' => 'Test description',
        ];

        $request = new BrandRequest;
        $rules = $request->rules();

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function test_name_only_is_valid(): void
    {
        $data = [
            'name' => 'Test Brand',
        ];

        $request = new BrandRequest;
        $rules = $request->rules();

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }

    public function test_all_fields_are_optional_except_name(): void
    {
        $data = [
            'name' => 'Test Brand',
            'slug' => 'test-brand',
            'description' => 'Test description',
            'logo' => 'https://example.com/logo.png',
            'meta_title' => 'Test SEO Title',
            'meta_description' => 'Test SEO description',
            'is_active' => true,
        ];

        $request = new BrandRequest;
        $rules = $request->rules();

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }

    public function test_logo_accepts_url_string(): void
    {
        $data = [
            'name' => 'Test Brand',
            'logo' => 'https://example.com/logo.png',
        ];

        $request = new BrandRequest;
        $rules = $request->rules();

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }

    public function test_logo_rejects_too_long_url(): void
    {
        $data = [
            'name' => 'Test Brand',
            'logo' => str_repeat('a', 501), // 501 characters, exceeds 500 limit
        ];

        $request = new BrandRequest;
        $rules = $request->rules();

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('logo', $validator->errors()->toArray());
    }
}
