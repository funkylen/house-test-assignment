<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testStore(): void
    {
        $body = [
            'title'              => 'Hello',
            'parent_category_id' => null,
        ];

        $response = $this->post(route('categories.store'), $body);

        $response->assertCreated();

        $this->assertDatabaseHas('categories', $body);
    }

    public function testStoreCategoryDepth4(): void
    {
        $rootCategory = Category::factory()->create();
        $categorySecondLevel = Category::factory()->for($rootCategory, 'parentCategory')->create();
        $categoryThirdLevel = Category::factory()->for($categorySecondLevel, 'parentCategory')->create();

        $body = [
            'title'              => 'Hello',
            'parent_category_id' => $categoryThirdLevel->id,
        ];

        $response = $this->post(route('categories.store'), $body);

        $response->assertUnprocessable();

        $this->assertDatabaseCount('categories', 3);
    }
}
