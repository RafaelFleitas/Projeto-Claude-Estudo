<?php

namespace Tests\Unit;

use App\Models\Contract;
use App\Models\User;
use App\Policies\ContractPolicy;
use PHPUnit\Framework\TestCase;

class ContractPolicyTest extends TestCase
{
    private ContractPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new ContractPolicy();
    }

    // -----------------------------------------------------------------------
    // viewAny
    // -----------------------------------------------------------------------

    public function test_admin_can_view_any_contracts(): void
    {
        $admin = User::factory()->admin()->make();

        $this->assertTrue($this->policy->viewAny($admin));
    }

    public function test_regular_user_can_view_any_contracts(): void
    {
        $user = User::factory()->make();

        $this->assertTrue($this->policy->viewAny($user));
    }

    // -----------------------------------------------------------------------
    // view
    // -----------------------------------------------------------------------

    public function test_admin_can_view_any_contract(): void
    {
        $admin    = User::factory()->admin()->make();
        $contract = Contract::factory()->make(['user_id' => 999]);

        $this->assertTrue($this->policy->view($admin, $contract));
    }

    public function test_regular_user_can_view_contract(): void
    {
        $user     = User::factory()->make();
        $contract = Contract::factory()->make(['user_id' => 999]);

        // ContractPolicy::view() returns true for all authenticated users
        $this->assertTrue($this->policy->view($user, $contract));
    }

    // -----------------------------------------------------------------------
    // create
    // -----------------------------------------------------------------------

    public function test_admin_can_create_contract(): void
    {
        $admin = User::factory()->admin()->make();

        $this->assertTrue($this->policy->create($admin));
    }

    public function test_regular_user_can_create_contract(): void
    {
        $user = User::factory()->make();

        $this->assertTrue($this->policy->create($user));
    }

    // -----------------------------------------------------------------------
    // update
    // -----------------------------------------------------------------------

    public function test_admin_can_update_any_contract(): void
    {
        $admin    = User::factory()->admin()->make();
        $contract = Contract::factory()->make(['user_id' => 999]);

        $this->assertTrue($this->policy->update($admin, $contract));
    }

    public function test_owner_can_update_own_contract(): void
    {
        $user     = User::factory()->make(['id' => 1]);
        $contract = Contract::factory()->make(['user_id' => 1]);

        $this->assertTrue($this->policy->update($user, $contract));
    }

    public function test_non_owner_cannot_update_contract(): void
    {
        $user     = User::factory()->make(['id' => 1]);
        $contract = Contract::factory()->make(['user_id' => 2]);

        $this->assertFalse($this->policy->update($user, $contract));
    }

    // -----------------------------------------------------------------------
    // delete
    // -----------------------------------------------------------------------

    public function test_admin_can_delete_any_contract(): void
    {
        $admin    = User::factory()->admin()->make();
        $contract = Contract::factory()->make(['user_id' => 999]);

        $this->assertTrue($this->policy->delete($admin, $contract));
    }

    public function test_regular_user_cannot_delete_any_contract(): void
    {
        $user     = User::factory()->make(['id' => 1]);
        $contract = Contract::factory()->make(['user_id' => 1]);

        $this->assertFalse($this->policy->delete($user, $contract));
    }

    public function test_non_owner_cannot_delete_contract(): void
    {
        $user     = User::factory()->make(['id' => 1]);
        $contract = Contract::factory()->make(['user_id' => 2]);

        $this->assertFalse($this->policy->delete($user, $contract));
    }

    // -----------------------------------------------------------------------
    // restore
    // -----------------------------------------------------------------------

    public function test_admin_can_restore_contract(): void
    {
        $admin    = User::factory()->admin()->make();
        $contract = Contract::factory()->make(['user_id' => 999]);

        $this->assertTrue($this->policy->restore($admin, $contract));
    }

    public function test_regular_user_cannot_restore_contract(): void
    {
        $user     = User::factory()->make(['id' => 1]);
        $contract = Contract::factory()->make(['user_id' => 1]);

        $this->assertFalse($this->policy->restore($user, $contract));
    }

    // -----------------------------------------------------------------------
    // generatePdf
    // -----------------------------------------------------------------------

    public function test_admin_can_generate_pdf(): void
    {
        $admin    = User::factory()->admin()->make();
        $contract = Contract::factory()->make(['user_id' => 999]);

        $this->assertTrue($this->policy->generatePdf($admin, $contract));
    }

    public function test_owner_can_generate_pdf(): void
    {
        $user     = User::factory()->make(['id' => 1]);
        $contract = Contract::factory()->make(['user_id' => 1]);

        $this->assertTrue($this->policy->generatePdf($user, $contract));
    }

    public function test_non_owner_can_also_generate_pdf(): void
    {
        // ContractPolicy::generatePdf() returns true for all authenticated users
        $user     = User::factory()->make(['id' => 1]);
        $contract = Contract::factory()->make(['user_id' => 2]);

        $this->assertTrue($this->policy->generatePdf($user, $contract));
    }
}
