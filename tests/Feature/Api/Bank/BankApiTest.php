<?php

namespace Tests\Feature\Api\Bank;

use App\Models\Bank;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BankApiTest extends TestCase
{
    use RefreshDatabase;
    protected $seed = false;

    /**
     * Initial setup: disables authentication middleware if necessary.
     */
    protected function setUp(): void
    {
        parent::setUp();
        // If the routes are protected by auth, uncomment the line below
        // $this->withoutMiddleware();
    }

    /**
     * Helper to create a bank in the database.
     */
    private function createBank(array $attributes = []): Bank
    {
        $deletedAt = $attributes['deleted_at'] ?? null;
        unset($attributes['deleted_at']);

        $bank = Bank::create(array_merge([
            'id'              => \Illuminate\Support\Str::uuid()->toString(),
            'bank_name'       => 'Test Bank ' . uniqid(),
            'short_name'      => 'TB' . rand(100, 999),
            'country_prefix'  => 'PT',
            'bank_prefix'     => str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
        ], $attributes));

        if ($deletedAt) {
            $bank->delete();
        }

        return $bank;
    }

    /** @test */
    public function test_index_returns_paginated_list_of_banks(): void
    {
        Bank::truncate();
        $bank1 = $this->createBank(['bank_name' => 'Alpha Bank']);
        $bank2 = $this->createBank(['bank_name' => 'Beta Bank']);
        $bank3 = $this->createBank(['bank_name' => 'Gamma Bank', 'deleted_at' => now()]); // deleted should not appear

        $response = $this->getJson('/api/v1/banks?page=1&per_page=2');

        $response->assertOk()
            ->assertJsonCount(2, 'data') // adjust according to pagination structure
            ->assertJsonFragment(['bank_name' => 'Alpha Bank'])
            ->assertJsonFragment(['bank_name' => 'Beta Bank'])
            ->assertJsonMissing(['bank_name' => 'Gamma Bank']);
    }

    /** @test */
    public function test_index_can_filter_by_bank_name(): void
    {
        $this->createBank(['bank_name' => 'Banco XYZ']);
        $this->createBank(['bank_name' => 'Outro Banco']);

        $response = $this->getJson('/api/v1/banks?bankNameFilter=XYZ');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['bank_name' => 'Banco XYZ']);
    }

    /** @test */
    public function test_index_can_filter_by_bank_id(): void
    {
        $bank = $this->createBank();
        $this->createBank();

        $response = $this->getJson("/api/v1/banks?bankIdFilter={$bank->id}");

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['id' => $bank->id]);
    }

    /** @test */
    public function test_store_creates_a_new_bank_with_valid_data(): void
    {
        $payload = [
            'bank_name'      => 'Banco Nacional',
            'short_name'     => 'BNAC',
            'country_prefix' => 'AO',
            'bank_prefix'    => '0050',
        ];

        $response = $this->postJson('/api/v1/banks', $payload);

        $response->assertCreated()
            ->assertJsonFragment(['bank_name' => 'Banco Nacional']);

        $this->assertDatabaseHas('banks', $payload);
    }

    /** @test */
    public function test_store_returns_validation_error_when_required_fields_are_missing(): void
    {
        $response = $this->postJson('/api/v1/banks', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['bank_name', 'short_name', 'country_prefix', 'bank_prefix']);
    }

    /** @test */
    public function test_store_returns_error_when_bank_name_already_exists(): void
    {
        $this->createBank(['bank_name' => 'Duplicado']);

        $response = $this->postJson('/api/v1/banks', [
            'bank_name'      => 'Duplicado',
            'short_name'     => 'DUP2',
            'country_prefix' => 'BR',
            'bank_prefix'    => '1234',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['bank_name']);
    }

    /** @test */
    public function test_store_returns_error_when_bank_prefix_is_not_4_characters(): void
    {
        $response = $this->postJson('/api/v1/banks', [
            'bank_name'      => 'Banco Curto',
            'short_name'     => 'BC',
            'country_prefix' => 'PT',
            'bank_prefix'    => '12',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['bank_prefix']);
    }

    /** @test */
    public function test_update_updates_existing_bank_with_partial_data(): void
    {
        $bank = $this->createBank([
            'bank_name' => 'Old Name',
            'bank_prefix' => '9999',
        ]);

        $payload = [
            'bank_name'  => 'New Name',
            'short_name' => 'NEW',
        ];

        $response = $this->putJson("/api/v1/banks/{$bank->id}", $payload);

        $response->assertOk()
            ->assertJsonFragment(['bank_name' => 'New Name', 'short_name' => 'NEW']);

        $this->assertDatabaseHas('banks', [
            'id' => $bank->id,
            'bank_name' => 'New Name',
            'short_name' => 'NEW',
            'bank_prefix' => '9999', // unchanged
        ]);
    }

    /** @test */
    public function test_update_ignores_unique_validation_for_own_record(): void
    {
        $bank = $this->createBank(['bank_name' => 'My Bank', 'short_name' => 'MB', 'bank_prefix' => '1111']);

        $response = $this->putJson("/api/v1/banks/{$bank->id}", [
            'bank_name' => 'My Bank', // same name
            'bank_prefix' => '1111',
        ]);

        $response->assertOk(); // should pass because it is the same record
    }

    /** @test */
    public function test_update_returns_error_if_id_does_not_exist(): void
    {
        $fakeId = \Illuminate\Support\Str::uuid()->toString();

        $response = $this->putJson("/api/v1/banks/{$fakeId}", [
            'bank_name' => 'Whatever',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['id']);
    }

    /** @test */
    public function test_destroy_moves_bank_to_trash_soft_delete(): void
    {
        // NOTE: The defined route is DELETE /{id} pointing to 'destroy'.
        // The current controller does not have the 'destroy' method, but 'moveTrashBank'.
        // This test assumes the correct action is soft-delete.
        // If the method does not exist, the test will fail. Adjust as needed.
        $bank = $this->createBank();
        $this->assertNull($bank->deleted_at);

        $response = $this->deleteJson("/api/v1/banks/{$bank->id}");

        // We expect success and that the bank has been soft-deleted.
        $response->assertOk(); // or assertNoContent(204)
        $this->assertSoftDeleted('banks', ['id' => $bank->id]);
    }

    /** @test */
    public function test_flat_trash_can_lists_banks_in_trash(): void
    {
        $activeBank = $this->createBank();
        $trashedBank = $this->createBank(['deleted_at' => now()]);

        $response = $this->getJson('/api/v1/banks/flat/trash/can');

        $response->assertOk()
            ->assertJsonCount(1, 'data') // adjust according to pagination
            ->assertJsonFragment(['id' => $trashedBank->id])
            ->assertJsonMissing(['id' => $activeBank->id]);
    }

    /** @test */
    public function test_recover_an_item_from_recycle_bin_restores_a_deleted_bank(): void
    {
        $bank = $this->createBank(['deleted_at' => now()]);

        $response = $this->putJson("/api/v1/banks/recover/one/{$bank->id}");

        $response->assertOk();
        $this->assertDatabaseHas('banks', [
            'id' => $bank->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function test_recover_an_item_returns_error_for_nonexistent_id(): void
    {
        $fakeId = \Illuminate\Support\Str::uuid()->toString();

        $response = $this->putJson("/api/v1/banks/recover/one/{$fakeId}");

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['id']);
    }

    /** @test */
    public function test_restore_all_banks_from_recycle_bin_restores_all_deleted_banks(): void
    {
        // The route is GET /restore/all/, which calls restoreAllPlansFromRecycleBin,
        // but the controller has restoreAllBanksFromRecycleBin.
        // This test assumes the correct name is mapped.
        $bank1 = $this->createBank(['deleted_at' => now()]);
        $bank2 = $this->createBank(['deleted_at' => now()]);
        $activeBank = $this->createBank();

        $response = $this->getJson('/api/v1/banks/restore/all/');

        $response->assertOk();
        $this->assertDatabaseHas('banks', ['id' => $bank1->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('banks', ['id' => $bank2->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('banks', ['id' => $activeBank->id, 'deleted_at' => null]); // remains unchanged
    }

    /** @test */
    public function test_permanently_delete_bank_removes_permanently(): void
    {
        $bank = $this->createBank();

        $response = $this->deleteJson("/api/v1/banks/permanently/delete/{$bank->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('banks', ['id' => $bank->id]);
    }

    /** @test */
    public function test_permanently_delete_bank_returns_error_for_invalid_id(): void
    {
        $fakeId = \Illuminate\Support\Str::uuid()->toString();

        $response = $this->deleteJson("/api/v1/banks/permanently/delete/{$fakeId}");

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['id']);
    }
}
