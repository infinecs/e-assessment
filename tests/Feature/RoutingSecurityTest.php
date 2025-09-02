<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoutingSecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that admin routes are protected and redirect unauthenticated users
     */
    public function test_admin_routes_require_authentication(): void
    {
        // Test admin route redirects to login
        $response = $this->get('/admin');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     * Test that user dashboard requires authentication
     */
    public function test_user_dashboard_requires_authentication(): void
    {
        $response = $this->get('/user');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     * Test that settings routes require authentication
     */
    public function test_settings_require_authentication(): void
    {
        $response = $this->get('/settings');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     * Test that quiz routes require participant authentication
     */
    public function test_quiz_routes_require_participant_authentication(): void
    {
        $eventCode = 'TEST123';
        
        $response = $this->get("/quiz/$eventCode/");
        $response->assertStatus(302);
        // Should redirect to participant registration
        $response->assertRedirect("/participantRegister/$eventCode");
    }

    /**
     * Test that participant registration is accessible without authentication
     */
    public function test_participant_registration_is_public(): void
    {
        $eventCode = 'TEST123';
        
        $response = $this->get("/participantRegister/$eventCode");
        $response->assertStatus(200);
    }

    /**
     * Test that login routes are accessible
     */
    public function test_login_routes_are_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    /**
     * Test that admin can access admin routes when authenticated
     */
    public function test_authenticated_admin_can_access_admin_routes(): void
    {
        // Create an admin user
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'roles' => 'admin',
        ]);

        // Act as the admin
        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
    }

    /**
     * Test that authenticated users can access user dashboard
     */
    public function test_authenticated_user_can_access_user_dashboard(): void
    {
        // Create a regular user
        $user = User::factory()->create([
            'email' => 'user@test.com',
            'roles' => 'user',
        ]);

        // Act as the user
        $response = $this->actingAs($user)->get('/user');
        $response->assertStatus(200);
    }
}