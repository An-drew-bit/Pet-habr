<?php

namespace Tests\Feature\App\Http\Controllers;

use Domain\Category\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_categories(): void
    {
        $this->markTestIncomplete();
    }

    public function test_category_show()
    {
        $category = Category::factory()->create([
            'id' => 6,
            'title' => 'test',
            'slug' => 'test'
        ]);

        $this->post('/api/category/test')
            ->assertOk()
            ->assertJsonPath('category.title', $category->title);
    }
}
