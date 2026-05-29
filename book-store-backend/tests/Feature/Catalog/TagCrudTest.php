<?php

declare(strict_types=1);

namespace Tests\Feature\Catalog;

use App\Infrastructure\Persistence\Models\TagModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

final class TagCrudTest extends TestCase
{
    use DatabaseTransactions;

    public function test_list_books_returns_paginated_response(): void
    {
        TagModel::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('tags.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta' => ['total', 'per_page', 'current_page', 'total_pages'],
            ]);
    }
}
