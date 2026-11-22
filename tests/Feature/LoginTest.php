<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_loads(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_user_can_log_in_with_phone_and_password(): void
    {
        $user = User::factory()->create(['phone' => '012345678']);

        $this->post('/login', ['phone' => '012345678', 'password' => 'password'])
            ->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_wrong_password_is_rejected(): void
    {
        User::factory()->create(['phone' => '012345678']);

        $this->post('/login', ['phone' => '012345678', 'password' => 'wrong'])
            ->assertSessionHasErrors('phone');

        $this->assertGuest();
    }

    public function test_login_is_locked_after_five_wrong_attempts(): void
    {
        User::factory()->create(['phone' => '012345678']);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['phone' => '012345678', 'password' => 'wrong']);
        }

        $this->post('/login', ['phone' => '012345678', 'password' => 'password'])
            ->assertSessionHasErrors('phone');

        $this->assertGuest();
    }

    public function test_dashboard_sends_each_role_to_its_own_page(): void
    {
        $admin = User::factory()->admin()->create();
        $tenant = User::factory()->create();

        $this->actingAs($admin)->get('/dashboard')->assertRedirect('/admin');
        $this->actingAs($tenant)->get('/dashboard')->assertRedirect('/tenant');
    }

    public function test_register_page_does_not_exist(): void
    {
        $this->get('/register')->assertNotFound();
    }

    public function test_user_can_log_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/logout')->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_language_switch_sets_cookie_and_changes_text(): void
    {
        $this->get('/lang/km')->assertRedirect()->assertCookie('locale', 'km');

        $this->withCookie('locale', 'km')->get('/login')->assertSee('ចូលគណនី');
    }
}