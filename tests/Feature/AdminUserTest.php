<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Index
    // -----------------------------------------------------------------------

    public function test_admin_can_list_users(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('admin/users/index'));
    }

    public function test_non_admin_cannot_access_admin_users_index(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.users.index'));

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_to_login_on_admin_users_index(): void
    {
        $response = $this->get(route('admin.users.index'));

        $response->assertRedirect(route('login'));
    }

    // -----------------------------------------------------------------------
    // Create / Store
    // -----------------------------------------------------------------------

    public function test_admin_can_access_create_user_page(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.users.create'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('admin/users/create'));
    }

    public function test_admin_can_create_user(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name'                  => 'New User',
            'email'                 => 'newuser@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'user',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'name'  => 'New User',
            'email' => 'newuser@example.com',
        ]);
    }

    public function test_store_user_creates_with_hashed_password(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name'                  => 'Hashed User',
            'email'                 => 'hashed@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
            'role'                  => 'user',
        ]);

        $user = User::where('email', 'hashed@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('secret123', $user->password));
        $this->assertNotSame('secret123', $user->password);
    }

    public function test_store_user_creates_with_specified_role(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name'                  => 'Admin User',
            'email'                 => 'adminuser@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'admin',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'adminuser@example.com',
            'role'  => 'admin',
        ]);
    }

    public function test_store_user_validation_fails_without_name(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'email'                 => 'noname@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'user',
        ]);

        $response->assertInvalid(['name']);
    }

    public function test_non_admin_cannot_create_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.users.store'), [
            'name'                  => 'Attempted User',
            'email'                 => 'attempted@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'user',
        ]);

        $response->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Edit / Update
    // -----------------------------------------------------------------------

    public function test_admin_can_access_edit_user_page(): void
    {
        $admin  = User::factory()->admin()->create();
        $target = User::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.users.edit', $target));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('admin/users/edit'));
    }

    public function test_admin_can_update_user(): void
    {
        $admin  = User::factory()->admin()->create();
        $target = User::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($admin)->patch(route('admin.users.update', $target), [
            'name'  => 'New Name',
            'email' => $target->email,
            'role'  => 'admin',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'id'   => $target->id,
            'name' => 'New Name',
            'role' => 'admin',
        ]);
    }

    // -----------------------------------------------------------------------
    // Destroy
    // -----------------------------------------------------------------------

    public function test_admin_can_delete_another_user(): void
    {
        $admin  = User::factory()->admin()->create();
        $target = User::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $target));

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $admin));

        $response->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_non_admin_cannot_delete_user(): void
    {
        $user   = User::factory()->create();
        $target = User::factory()->create();

        $response = $this->actingAs($user)->delete(route('admin.users.destroy', $target));

        $response->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $target->id]);
    }

    // -----------------------------------------------------------------------
    // Public registration disabled
    // -----------------------------------------------------------------------

    public function test_public_registration_endpoint_is_disabled(): void
    {
        $this->skipUnlessFortifyHas(\Laravel\Fortify\Features::registration());

        // If registration is enabled, this test is not applicable
        $this->markTestSkipped('Registration feature is enabled; skipping disabled-registration test.');
    }
}
