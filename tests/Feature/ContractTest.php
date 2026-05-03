<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OwenIt\Auditing\Models\Audit;
use Tests\TestCase;

class ContractTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Index
    // -----------------------------------------------------------------------

    public function test_guest_is_redirected_to_login_on_index(): void
    {
        $response = $this->get(route('contracts.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_list_contracts(): void
    {
        $user = User::factory()->create();
        Contract::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('contracts.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('contracts/index'));
    }

    public function test_admin_can_list_all_contracts(): void
    {
        $admin = User::factory()->admin()->create();
        $other = User::factory()->create();

        Contract::factory()->count(2)->create(['user_id' => $admin->id]);
        Contract::factory()->count(3)->create(['user_id' => $other->id]);

        $response = $this->actingAs($admin)->get(route('contracts.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('contracts/index'));
    }

    // -----------------------------------------------------------------------
    // Create / Store
    // -----------------------------------------------------------------------

    public function test_authenticated_user_can_access_create_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('contracts.create'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('contracts/create'));
    }

    public function test_authenticated_user_can_store_contract(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('contracts.store'), [
            'contrato' => 'CT-0001/2025',
            'status'   => 'pending',
        ]);

        $response->assertRedirect(route('contracts.index'));
        $this->assertDatabaseHas('contracts', [
            'contrato' => 'CT-0001/2025',
            'user_id'  => $user->id,
        ]);
    }

    public function test_store_contract_sets_correct_user_id(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('contracts.store'), [
            'contrato' => 'CT-9999/2025',
            'status'   => 'active',
        ]);

        $contract = Contract::where('contrato', 'CT-9999/2025')->first();
        $this->assertNotNull($contract);
        $this->assertSame($user->id, $contract->user_id);
    }

    public function test_store_contract_validation_fails_without_contrato(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('contracts.store'), [
            'status' => 'pending',
        ]);

        $response->assertInvalid(['contrato']);
    }

    public function test_store_contract_validation_fails_without_status(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('contracts.store'), [
            'contrato' => 'CT-1234/2025',
        ]);

        $response->assertInvalid(['status']);
    }

    // -----------------------------------------------------------------------
    // Show
    // -----------------------------------------------------------------------

    public function test_authenticated_user_can_view_own_contract(): void
    {
        $user     = User::factory()->create();
        $contract = Contract::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('contracts.show', $contract));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('contracts/show'));
    }

    public function test_admin_can_view_any_contract(): void
    {
        $admin    = User::factory()->admin()->create();
        $owner    = User::factory()->create();
        $contract = Contract::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($admin)->get(route('contracts.show', $contract));

        $response->assertOk();
    }

    public function test_regular_user_can_view_other_users_contract(): void
    {
        // ContractPolicy::view() returns true for all authenticated users
        $user     = User::factory()->create();
        $other    = User::factory()->create();
        $contract = Contract::factory()->create(['user_id' => $other->id]);

        $response = $this->actingAs($user)->get(route('contracts.show', $contract));

        $response->assertOk();
    }

    // -----------------------------------------------------------------------
    // Update
    // -----------------------------------------------------------------------

    public function test_owner_can_update_own_contract(): void
    {
        $user     = User::factory()->create();
        $contract = Contract::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->patch(route('contracts.update', $contract), [
            'contrato' => 'CT-UPDATED/2025',
            'status'   => 'active',
        ]);

        $response->assertRedirect(route('contracts.show', $contract));
        $this->assertDatabaseHas('contracts', ['contrato' => 'CT-UPDATED/2025']);
    }

    public function test_admin_can_update_any_contract(): void
    {
        $admin    = User::factory()->admin()->create();
        $owner    = User::factory()->create();
        $contract = Contract::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($admin)->patch(route('contracts.update', $contract), [
            'contrato' => 'CT-ADMIN-UPDATED/2025',
            'status'   => 'completed',
        ]);

        $response->assertRedirect(route('contracts.show', $contract));
        $this->assertDatabaseHas('contracts', ['contrato' => 'CT-ADMIN-UPDATED/2025']);
    }

    public function test_non_owner_cannot_update_contract(): void
    {
        $user     = User::factory()->create();
        $other    = User::factory()->create();
        $contract = Contract::factory()->create(['user_id' => $other->id]);

        $response = $this->actingAs($user)->patch(route('contracts.update', $contract), [
            'contrato' => 'CT-HACKED/2025',
            'status'   => 'active',
        ]);

        $response->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Destroy
    // -----------------------------------------------------------------------

    public function test_admin_can_delete_contract(): void
    {
        $admin    = User::factory()->admin()->create();
        $contract = Contract::factory()->create();

        $response = $this->actingAs($admin)->delete(route('contracts.destroy', $contract));

        $response->assertRedirect(route('contracts.index'));
        $this->assertSoftDeleted('contracts', ['id' => $contract->id]);
    }

    public function test_non_admin_cannot_delete_contract(): void
    {
        $user     = User::factory()->create();
        $contract = Contract::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('contracts.destroy', $contract));

        $response->assertForbidden();
        $this->assertNotSoftDeleted('contracts', ['id' => $contract->id]);
    }

    // -----------------------------------------------------------------------
    // Soft delete visibility
    // -----------------------------------------------------------------------

    public function test_deleted_contract_does_not_appear_in_index(): void
    {
        $admin    = User::factory()->admin()->create();
        $contract = Contract::factory()->create(['contrato' => 'CT-DELETED/2025']);

        $contract->delete();

        $response = $this->actingAs($admin)->get(route('contracts.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('contracts/index')
            ->where('contracts.data', fn ($data) => collect($data)->doesntContain('id', $contract->id))
        );
    }

    // -----------------------------------------------------------------------
    // Audit trail
    // -----------------------------------------------------------------------

    public function test_creating_contract_creates_audit_record(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('contracts.store'), [
            'contrato' => 'CT-AUDIT/2025',
            'status'   => 'pending',
        ]);

        $this->assertGreaterThan(0, Audit::where('event', 'created')
            ->where('auditable_type', Contract::class)
            ->count());
    }

    public function test_updating_contract_creates_audit_record(): void
    {
        $user     = User::factory()->create();
        $contract = Contract::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->patch(route('contracts.update', $contract), [
            'contrato' => 'CT-AUDIT-UPDATE/2025',
            'status'   => 'active',
        ]);

        $this->assertGreaterThan(0, Audit::where('event', 'updated')
            ->where('auditable_type', Contract::class)
            ->where('auditable_id', $contract->id)
            ->count());
    }

    public function test_deleting_contract_creates_audit_record(): void
    {
        $admin    = User::factory()->admin()->create();
        $contract = Contract::factory()->create();

        $this->actingAs($admin)->delete(route('contracts.destroy', $contract));

        $this->assertGreaterThan(0, Audit::where('event', 'deleted')
            ->where('auditable_type', Contract::class)
            ->where('auditable_id', $contract->id)
            ->count());
    }
}
