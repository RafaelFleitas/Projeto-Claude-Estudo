<?php

namespace Tests\Unit;

use App\Models\User;
use App\Policies\UserPolicy;
use PHPUnit\Framework\TestCase;

class UserPolicyTest extends TestCase
{
    private UserPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new UserPolicy();
    }

    // -----------------------------------------------------------------------
    // viewAny
    // -----------------------------------------------------------------------

    public function test_admin_can_view_any_users(): void
    {
        $admin = User::factory()->admin()->make();

        $this->assertTrue($this->policy->viewAny($admin));
    }

    public function test_regular_user_cannot_view_any_users(): void
    {
        $user = User::factory()->make();

        $this->assertFalse($this->policy->viewAny($user));
    }

    // -----------------------------------------------------------------------
    // view
    // -----------------------------------------------------------------------

    public function test_admin_can_view_any_user(): void
    {
        $admin  = User::factory()->admin()->make();
        $target = User::factory()->make(['id' => 99]);

        $this->assertTrue($this->policy->view($admin, $target));
    }

    public function test_regular_user_can_view_themselves(): void
    {
        $user = User::factory()->make(['id' => 1]);

        $this->assertTrue($this->policy->view($user, $user));
    }

    public function test_regular_user_cannot_view_other_user(): void
    {
        $user  = User::factory()->make(['id' => 1]);
        $other = User::factory()->make(['id' => 2]);

        $this->assertFalse($this->policy->view($user, $other));
    }

    // -----------------------------------------------------------------------
    // create
    // -----------------------------------------------------------------------

    public function test_admin_can_create_user(): void
    {
        $admin = User::factory()->admin()->make();

        $this->assertTrue($this->policy->create($admin));
    }

    public function test_regular_user_cannot_create_user(): void
    {
        $user = User::factory()->make();

        $this->assertFalse($this->policy->create($user));
    }

    // -----------------------------------------------------------------------
    // update
    // -----------------------------------------------------------------------

    public function test_admin_can_update_any_user(): void
    {
        $admin  = User::factory()->admin()->make();
        $target = User::factory()->make(['id' => 99]);

        $this->assertTrue($this->policy->update($admin, $target));
    }

    public function test_regular_user_can_update_themselves(): void
    {
        $user = User::factory()->make(['id' => 1]);

        $this->assertTrue($this->policy->update($user, $user));
    }

    public function test_regular_user_cannot_update_other_user(): void
    {
        $user  = User::factory()->make(['id' => 1]);
        $other = User::factory()->make(['id' => 2]);

        $this->assertFalse($this->policy->update($user, $other));
    }

    // -----------------------------------------------------------------------
    // delete
    // -----------------------------------------------------------------------

    public function test_admin_can_delete_another_user(): void
    {
        $admin  = User::factory()->admin()->make(['id' => 1]);
        $target = User::factory()->make(['id' => 2]);

        $this->assertTrue($this->policy->delete($admin, $target));
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = User::factory()->admin()->make(['id' => 1]);

        $this->assertFalse($this->policy->delete($admin, $admin));
    }

    public function test_regular_user_cannot_delete_any_user(): void
    {
        $user   = User::factory()->make(['id' => 1]);
        $target = User::factory()->make(['id' => 2]);

        $this->assertFalse($this->policy->delete($user, $target));
    }

    public function test_regular_user_cannot_delete_themselves(): void
    {
        $user = User::factory()->make(['id' => 1]);

        $this->assertFalse($this->policy->delete($user, $user));
    }
}
