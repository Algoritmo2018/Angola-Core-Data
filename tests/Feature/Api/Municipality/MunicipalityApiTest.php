<?php

namespace Tests\Feature\Api\Municipality;

use App\Models\Municipality;
use App\Models\Province;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MunicipalityApiTest extends TestCase
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

    private function createMunicipality(array $attributes = []): Municipality
    {
        $deletedAt = $attributes['deleted_at'] ?? null;
        unset($attributes['deleted_at']);

        $provinceId = $attributes['province_id'] ?? null;
        if ($provinceId === null) {
            $province = $this->createProvince(['id' => rand(1000, 9999)]);
            $provinceId = $province->id;
        }

        $municipality = new Municipality(array_merge([
            'name' => 'Test Municipality ' . uniqid(),
            'province_id' => $provinceId,
        ], $attributes));
        $municipality->save();

        if ($deletedAt) {
            $municipality->delete();
        }

        return $municipality;
    }

    public function test_index_returns_paginated_list_of_municipalities(): void
    {
        $this->createMunicipality(['name' => 'Luanda']);
        $this->createMunicipality(['name' => 'Benguela']);
        $this->createMunicipality(['name' => 'Huila', 'deleted_at' => now()]);

        $response = $this->getJson('/api/v1/municipalities?page=1&per_page=2');

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['name' => 'Luanda'])
            ->assertJsonFragment(['name' => 'Benguela'])
            ->assertJsonMissing(['name' => 'Huila']);
    }

    public function test_index_can_filter_municipalities_by_name(): void
    {
        $this->createMunicipality(['name' => 'Viana']);
        $this->createMunicipality(['name' => 'Cazenga']);

        $response = $this->getJson('/api/v1/municipalities?filter=Viana');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'Viana']);
    }

    public function test_store_creates_a_new_municipality_with_valid_data(): void
    {
        $province = $this->createProvince();

        $payload = [
            'name' => 'Viana',
            'province_id' => $province->id,
        ];

        $response = $this->postJson('/api/v1/municipalities', $payload);

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Viana']);

        $this->assertDatabaseHas('municipalities', ['name' => 'Viana']);
    }

    public function test_store_returns_validation_error_when_required_fields_are_missing(): void
    {
        $response = $this->postJson('/api/v1/municipalities', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'province_id']);
    }

    public function test_store_returns_validation_error_when_province_does_not_exist(): void
    {
        $response = $this->postJson('/api/v1/municipalities', [
            'name' => 'Talatona',
            'province_id' => 999999,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['province_id']);
    }

    public function test_update_updates_existing_municipality(): void
    {
        $municipality = $this->createMunicipality(['name' => 'Old Name']);
        $newProvince = $this->createProvince();

        $response = $this->putJson("/api/v1/municipalities/{$municipality->id}", [
            'id' => $municipality->id,
            'name' => 'New Name',
            'province_id' => $newProvince->id,
        ]);

        $response->assertOk()
            ->assertJsonFragment(['name' => 'New Name']);

        $this->assertDatabaseHas('municipalities', [
            'id' => $municipality->id,
            'name' => 'New Name',
            'province_id' => $newProvince->id,
        ]);
    }

    public function test_destroy_soft_deletes_a_municipality(): void
    {
        $municipality = $this->createMunicipality();

        $response = $this->deleteJson("/api/v1/municipalities/{$municipality->id}");

        $response->assertNoContent();
        $this->assertSoftDeleted('municipalities', ['id' => $municipality->id]);
    }

    public function test_show_deleted_returns_only_trashed_municipalities(): void
    {
        $active = $this->createMunicipality(['name' => 'Active']);
        $trashed = $this->createMunicipality(['name' => 'Trashed', 'deleted_at' => now()]);

        $response = $this->getJson('/api/v1/municipalities/show/deleted?per_page=15');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'Trashed'])
            ->assertJsonMissing(['name' => 'Active']);
    }

    public function test_restore_all_restores_every_trashed_municipality(): void
    {
        $trashed1 = $this->createMunicipality(['name' => 'Municipality A', 'deleted_at' => now()]);
        $trashed2 = $this->createMunicipality(['name' => 'Municipality B', 'deleted_at' => now()]);
        $active = $this->createMunicipality(['name' => 'Active']);

        $response = $this->getJson('/api/v1/municipalities/restore_all');

        $response->assertOk();
        $this->assertDatabaseHas('municipalities', ['id' => $trashed1->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('municipalities', ['id' => $trashed2->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('municipalities', ['id' => $active->id, 'deleted_at' => null]);
    }

    public function test_restore_one_restores_a_single_trashed_municipality(): void
    {
        $municipality = $this->createMunicipality(['name' => 'To Restore', 'deleted_at' => now()]);

        $response = $this->getJson("/api/v1/municipalities/restore_one/{$municipality->id}");

        $response->assertOk();
        $this->assertDatabaseHas('municipalities', ['id' => $municipality->id, 'deleted_at' => null]);
    }
}
