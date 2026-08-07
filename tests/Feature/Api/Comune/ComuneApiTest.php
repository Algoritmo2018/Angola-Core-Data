<?php

namespace Tests\Feature\Api\Comune;

use App\Models\Comune;
use App\Models\Municipality;
use App\Models\Province;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComuneApiTest extends TestCase
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

    private function createComune(array $attributes = []): Comune
    {
        $deletedAt = $attributes['deleted_at'] ?? null;
        unset($attributes['deleted_at']);

        $municipalityId = $attributes['municipality_id'] ?? null;
        if ($municipalityId === null) {
            $municipality = $this->createMunicipality(['id' => rand(1000, 9999)]);
            $municipalityId = $municipality->id;
        }

        $comune = new Comune(array_merge([
            'name' => 'Test Comune ' . uniqid(),
            'municipality_id' => $municipalityId,
        ], $attributes));
        $comune->save();

        if ($deletedAt) {
            $comune->delete();
        }

        return $comune;
    }

    public function test_index_returns_paginated_list_of_comunes(): void
    {
        $this->createComune(['name' => 'Luanda']);
        $this->createComune(['name' => 'Benguela']);
        $this->createComune(['name' => 'Huila', 'deleted_at' => now()]);

        $response = $this->getJson('/api/v1/comunes?page=1&per_page=2');

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['name' => 'Luanda'])
            ->assertJsonFragment(['name' => 'Benguela'])
            ->assertJsonMissing(['name' => 'Huila']);
    }

    public function test_index_can_filter_comunes_by_name(): void
    {
        $this->createComune(['name' => 'Viana']);
        $this->createComune(['name' => 'Cazenga']);

        $response = $this->getJson('/api/v1/comunes?filter=Viana');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'Viana']);
    }

    public function test_store_creates_a_new_comune_with_valid_data(): void
    {
        $municipality = $this->createMunicipality();

        $payload = [
            'name' => 'Maianga',
            'municipality_id' => $municipality->id,
        ];

        $response = $this->postJson('/api/v1/comunes', $payload);

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Maianga']);

        $this->assertDatabaseHas('comunes', ['name' => 'Maianga']);
    }

    public function test_store_returns_validation_error_when_required_fields_are_missing(): void
    {
        $response = $this->postJson('/api/v1/comunes', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'municipality_id']);
    }

    public function test_store_returns_validation_error_when_municipality_does_not_exist(): void
    {
        $response = $this->postJson('/api/v1/comunes', [
            'name' => 'Talatona',
            'municipality_id' => 999999,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['municipality_id']);
    }

    public function test_update_updates_existing_comune(): void
    {
        $comune = $this->createComune(['name' => 'Old Name']);
        $newMunicipality = $this->createMunicipality();

        $response = $this->putJson("/api/v1/comunes/{$comune->id}", [
            'id' => $comune->id,
            'name' => 'New Name',
            'municipality_id' => $newMunicipality->id,
        ]);

        $response->assertOk()
            ->assertJsonFragment(['name' => 'New Name']);

        $this->assertDatabaseHas('comunes', [
            'id' => $comune->id,
            'name' => 'New Name',
            'municipality_id' => $newMunicipality->id,
        ]);
    }

    public function test_destroy_soft_deletes_a_comune(): void
    {
        $comune = $this->createComune();

        $response = $this->deleteJson("/api/v1/comunes/{$comune->id}");

        $response->assertNoContent();
        $this->assertSoftDeleted('comunes', ['id' => $comune->id]);
    }

    public function test_show_deleted_returns_only_trashed_comunes(): void
    {
        $active = $this->createComune(['name' => 'Active']);
        $trashed = $this->createComune(['name' => 'Trashed', 'deleted_at' => now()]);

        $response = $this->getJson('/api/v1/comunes/show/deleted?per_page=15');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'Trashed'])
            ->assertJsonMissing(['name' => 'Active']);
    }

    public function test_restore_all_restores_every_trashed_comune(): void
    {
        $trashed1 = $this->createComune(['name' => 'Comune A', 'deleted_at' => now()]);
        $trashed2 = $this->createComune(['name' => 'Comune B', 'deleted_at' => now()]);
        $active = $this->createComune(['name' => 'Active']);

        $response = $this->getJson('/api/v1/comunes/restore_all');

        $response->assertOk();
        $this->assertDatabaseHas('comunes', ['id' => $trashed1->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('comunes', ['id' => $trashed2->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('comunes', ['id' => $active->id, 'deleted_at' => null]);
    }

    public function test_restore_one_restores_a_single_trashed_comune(): void
    {
        $comune = $this->createComune(['name' => 'To Restore', 'deleted_at' => now()]);

        $response = $this->getJson("/api/v1/comunes/restore_one/{$comune->id}");

        $response->assertOk();
        $this->assertDatabaseHas('comunes', ['id' => $comune->id, 'deleted_at' => null]);
    }
}
