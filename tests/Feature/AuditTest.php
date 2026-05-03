<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OwenIt\Auditing\Models\Audit;
use Tests\TestCase;

class AuditTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Access control
    // -----------------------------------------------------------------------

    public function test_guest_is_redirected_to_login_on_audits_index(): void
    {
        $response = $this->get(route('audits.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_access_audits_index(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('audits.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('audits/index'));
    }

    public function test_regular_user_cannot_access_audits_index(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('audits.index'));

        $response->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Filters
    // -----------------------------------------------------------------------

    public function test_audits_index_filters_by_event(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('audits.index', ['event' => 'created']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('audits/index')
            ->where('filters.event', 'created')
        );
    }

    public function test_audits_index_filters_by_module(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('audits.index', ['module' => 'contracts']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('audits/index')
            ->where('filters.module', 'contracts')
        );
    }

    public function test_audits_index_filters_by_date_range(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('audits.index', [
            'date_from' => '2025-01-01',
            'date_to'   => '2025-12-31',
        ]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('audits/index')
            ->where('filters.date_from', '2025-01-01')
            ->where('filters.date_to', '2025-12-31')
        );
    }

    public function test_audits_index_filters_by_user_id(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->create();

        $response = $this->actingAs($admin)->get(route('audits.index', ['user_id' => $user->id]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('audits/index')
            ->where('filters.user_id', (string) $user->id)
        );
    }

    // -----------------------------------------------------------------------
    // Audit records created by contract operations
    // -----------------------------------------------------------------------

    public function test_creating_contract_creates_audit_with_event_created(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('contracts.store'), [
            'contrato' => 'CT-AUDIT-EVENT/2025',
            'status'   => 'pending',
        ]);

        $this->assertDatabaseHas('audits', [
            'auditable_type' => Contract::class,
            'event'          => 'created',
        ]);
    }

    public function test_updating_contract_creates_audit_with_event_updated(): void
    {
        $user     = User::factory()->create();
        $contract = Contract::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->patch(route('contracts.update', $contract), [
            'contrato' => 'CT-UPDATED-AUDIT/2025',
            'status'   => 'active',
        ]);

        $this->assertDatabaseHas('audits', [
            'auditable_type' => Contract::class,
            'auditable_id'   => $contract->id,
            'event'          => 'updated',
        ]);
    }

    public function test_deleting_contract_creates_audit_with_event_deleted(): void
    {
        $admin    = User::factory()->admin()->create();
        $contract = Contract::factory()->create();

        $this->actingAs($admin)->delete(route('contracts.destroy', $contract));

        $this->assertDatabaseHas('audits', [
            'auditable_type' => Contract::class,
            'auditable_id'   => $contract->id,
            'event'          => 'deleted',
        ]);
    }
}
