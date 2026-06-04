<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/connexion');
    }

    public function test_admin_can_open_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/dashboard')->assertOk();
    }

    public function test_guest_can_register_an_agent_account(): void
    {
        $response = $this->post('/inscription', [
            'name' => 'New Agent',
            'email' => 'new.agent@example.com',
            'password' => 'Secure123',
            'password_confirmation' => 'Secure123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'new.agent@example.com', 'role' => 'agent']);
    }

    public function test_locale_can_be_changed_to_arabic(): void
    {
        $this->get('/langue/ar')->assertRedirect();
        $this->get('/connexion')->assertSee('تسجيل دخول آمن');
    }

    public function test_locale_is_preserved_after_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['locale' => 'ar'])
            ->post('/deconnexion')
            ->assertRedirect('/connexion');

        $this->get('/connexion')->assertSee('تسجيل دخول آمن');
    }
}
