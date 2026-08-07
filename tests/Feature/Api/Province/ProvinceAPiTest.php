<?php

namespace Tests\Feature\Api\Province;

use App\Models\Province;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProvinceApiTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = false;

    private function createProvince(array $attributes = []): Province
    {
        $deletedAt = $attributes['deleted_at'] ?? null;
        unset($attributes['deleted_at']);

        $province = new Province(array_merge([
            'name' => 'Test Province ' . uniqid(),
        ], $attributes));
        $province->save();

        if ($deletedAt) {
            $province->delete();
        }

        return $province;
    }

    public function test_index_returns_paginated_list_of_provinces(): void
    {
        $this->createProvince(['name' => 'Luanda']);
        $this->createProvince(['name' => 'Benguela']);
        $this->createProvince(['name' => 'Huila', 'deleted_at' => now()]);

        $response = $this->getJson('/api/v1/provinces?page=1&per_page=2');

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['name' => 'Luanda'])
            ->assertJsonFragment(['name' => 'Benguela'])
            ->assertJsonMissing(['name' => 'Huila']);
    }

    public function test_index_can_filter_provinces_by_name(): void
    {
        $this->createProvince(['name' => 'Luanda']);
        $this->createProvince(['name' => 'Benguela']);

        $response = $this->getJson('/api/v1/provinces?filter=Luanda');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'Luanda']);
    }

    public function test_store_creates_a_new_province_with_valid_data(): void
    {
        $payload = ['name' => 'Moxico'];

        $response = $this->postJson('/api/v1/provinces', $payload);

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Moxico']);

        $this->assertDatabaseHas('provinces', $payload);
    }

    public function test_store_returns_validation_error_when_name_is_missing(): void
    {
        $response = $this->postJson('/api/v1/provinces', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_store_returns_validation_error_when_name_is_duplicate(): void
    {
        $this->createProvince(['name' => 'Luanda']);

        $response = $this->postJson('/api/v1/provinces', ['name' => 'Luanda']);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_update_updates_an_existing_province(): void
    {
        $province = $this->createProvince(['name' => 'Old Name']);

        $response = $this->putJson("/api/v1/provinces/{$province->id}", [
            'id' => $province->id,
            'name' => 'New Name',
        ]);

        $response->assertOk()
            ->assertJsonFragment(['name' => 'New Name']);

        $this->assertDatabaseHas('provinces', [
            'id' => $province->id,
            'name' => 'New Name',
        ]);
    }

    public function test_update_returns_404_when_province_does_not_exist(): void
    {
        $response = $this->putJson('/api/v1/provinces/9999', [
            'id' => 9999,
            'name' => 'Some Name',
        ]);

        $response->assertNotFound()
            ->assertJson([
                'success' => false,
                'message' => 'Province not found',
            ]);
    }

    public function test_destroy_soft_deletes_a_province(): void
    {
        $province = $this->createProvince();

        $response = $this->deleteJson("/api/v1/provinces/{$province->id}");

        $response->assertNoContent();
        $this->assertSoftDeleted('provinces', ['id' => $province->id]);
    }

    public function test_destroy_returns_404_if_province_not_found(): void
    {
        $response = $this->deleteJson('/api/v1/provinces/9999');

        $response->assertNotFound()
            ->assertJson([
                'success' => false,
                'message' => 'Province not found',
            ]);
    }

    public function test_show_deleted_returns_only_trashed_provinces(): void
    {
        $active = $this->createProvince(['name' => 'Active']);
        $trashed = $this->createProvince(['name' => 'Trashed', 'deleted_at' => now()]);

        $response = $this->getJson('/api/v1/provinces/show/deleted?per_page=15');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'Trashed'])
            ->assertJsonMissing(['name' => 'Active']);
    }

    public function test_restore_all_restores_every_trashed_province(): void
    {
        $trashed1 = $this->createProvince(['name' => 'Province A', 'deleted_at' => now()]);
        $trashed2 = $this->createProvince(['name' => 'Province B', 'deleted_at' => now()]);
        $active = $this->createProvince(['name' => 'Active']);

        $response = $this->getJson('/api/v1/provinces/restore_all');

        $response->assertOk();
        $this->assertDatabaseHas('provinces', ['id' => $trashed1->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('provinces', ['id' => $trashed2->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('provinces', ['id' => $active->id, 'deleted_at' => null]);
    }

    public function test_restore_one_restores_a_single_trashed_province(): void
    {
        $province = $this->createProvince(['name' => 'To Restore', 'deleted_at' => now()]);

        $response = $this->getJson("/api/v1/provinces/restore_one/{$province->id}");

        $response->assertOk();
        $this->assertDatabaseHas('provinces', ['id' => $province->id, 'deleted_at' => null]);
    }
}
