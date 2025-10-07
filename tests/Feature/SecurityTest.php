<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $superAdmin;
    protected $departmentAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'role' => 'student',
            'department' => 'Bachelor of Science in Information Technology'
        ]);

        $this->superAdmin = Admin::factory()->create([
            'role' => 'super_admin',
            'department' => 'Administration'
        ]);

        $this->departmentAdmin = Admin::factory()->create([
            'role' => 'department_admin',
            'department' => 'Bachelor of Science in Information Technology'
        ]);
    }

    /** @test */
    public function sql_injection_attempts_are_blocked()
    {
        $this->actingAs($this->user);

        $maliciousInput = "'; DROP TABLE users; --";

        $response = $this->post('/api/chatbot', [
            'message' => $maliciousInput
        ]);

        // Should not cause database error
        $response->assertStatus(200);
        
        // Verify users table still exists
        $this->assertDatabaseHas('users', ['id' => $this->user->id]);
    }

    /** @test */
    public function xss_attempts_are_sanitized()
    {
        $this->actingAs($this->user);

        $xssPayload = '<script>alert("XSS")</script>';

        $response = $this->post('/api/chatbot', [
            'message' => $xssPayload
        ]);

        $response->assertStatus(200);
        
        // Check that script tags are not present in response
        $response->assertDontSee('<script>');
        $response->assertDontSee('alert("XSS")');
    }

    /** @test */
    public function csrf_protection_is_enabled()
    {
        $this->actingAs($this->user);

        // Disable CSRF for this test to verify it's normally enabled
        $this->withoutMiddleware();

        $response = $this->post('/api/chatbot', [
            'message' => 'Test message'
        ]);

        // Should fail without CSRF token
        $response->assertStatus(419); // CSRF token mismatch
    }

    /** @test */
    public function password_requirements_are_enforced()
    {
        $response = $this->post('/ms365/register', [
            'email' => 'test@student.mcc.edu.ph',
            'first_name' => 'John',
            'surname' => 'Doe',
            'role' => 'student',
            'department' => 'Bachelor of Science in Information Technology',
            'year_level' => '1st Year',
            'password' => '123', // Too short
            'password_confirmation' => '123'
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function email_validation_is_enforced()
    {
        $response = $this->post('/ms365/register', [
            'email' => 'invalid-email',
            'first_name' => 'John',
            'surname' => 'Doe',
            'role' => 'student',
            'department' => 'Bachelor of Science in Information Technology',
            'year_level' => '1st Year',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function name_validation_prevents_dangerous_characters()
    {
        $response = $this->post('/ms365/register', [
            'email' => 'test@student.mcc.edu.ph',
            'first_name' => 'John<script>alert("xss")</script>',
            'surname' => 'Doe',
            'role' => 'student',
            'department' => 'Bachelor of Science in Information Technology',
            'year_level' => '1st Year',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertSessionHasErrors(['first_name']);
    }

    /** @test */
    public function department_validation_prevents_invalid_departments()
    {
        $response = $this->post('/ms365/register', [
            'email' => 'test@student.mcc.edu.ph',
            'first_name' => 'John',
            'surname' => 'Doe',
            'role' => 'student',
            'department' => 'Invalid Department',
            'year_level' => '1st Year',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertSessionHasErrors(['department']);
    }

    /** @test */
    public function year_level_validation_prevents_invalid_levels()
    {
        $response = $this->post('/ms365/register', [
            'email' => 'test@student.mcc.edu.ph',
            'first_name' => 'John',
            'surname' => 'Doe',
            'role' => 'student',
            'department' => 'Bachelor of Science in Information Technology',
            'year_level' => 'Invalid Year',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertSessionHasErrors(['year_level']);
    }

    /** @test */
    public function role_validation_prevents_invalid_roles()
    {
        $response = $this->post('/ms365/register', [
            'email' => 'test@student.mcc.edu.ph',
            'first_name' => 'John',
            'surname' => 'Doe',
            'role' => 'invalid_role',
            'department' => 'Bachelor of Science in Information Technology',
            'year_level' => '1st Year',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertSessionHasErrors(['role']);
    }

    /** @test */
    public function passwords_are_hashed_properly()
    {
        $password = 'password123';
        
        $response = $this->post('/ms365/register', [
            'email' => 'test@student.mcc.edu.ph',
            'first_name' => 'John',
            'surname' => 'Doe',
            'role' => 'student',
            'department' => 'Bachelor of Science in Information Technology',
            'year_level' => '1st Year',
            'password' => $password,
            'password_confirmation' => $password
        ]);

        $user = User::where('ms365_account', 'test@student.mcc.edu.ph')->first();
        
        $this->assertNotNull($user);
        $this->assertNotEquals($password, $user->password);
        $this->assertTrue(Hash::check($password, $user->password));
    }

    /** @test */
    public function admin_routes_require_authentication()
    {
        $response = $this->get('/superadmin/dashboard');
        
        $response->assertRedirect('/login');
    }

    /** @test */
    public function admin_routes_require_admin_authentication()
    {
        $this->actingAs($this->user); // Regular user, not admin
        
        $response = $this->get('/superadmin/dashboard');
        
        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function department_admin_cannot_access_superadmin_routes()
    {
        $this->actingAs($this->departmentAdmin, 'admin');
        
        $response = $this->get('/superadmin/dashboard');
        
        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function superadmin_can_access_all_admin_routes()
    {
        $this->actingAs($this->superAdmin, 'admin');
        
        $response = $this->get('/superadmin/dashboard');
        
        $response->assertStatus(200);
    }

    /** @test */
    public function content_creation_requires_proper_authorization()
    {
        $this->actingAs($this->user); // Regular user
        
        $announcementData = [
            'title' => 'Unauthorized Announcement',
            'content' => 'This should not work',
            'is_published' => true
        ];

        $response = $this->post('/superadmin/announcements', $announcementData);
        
        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function content_editing_requires_proper_authorization()
    {
        $this->actingAs($this->user); // Regular user
        
        $announcement = Announcement::factory()->create([
            'admin_id' => $this->superAdmin->id
        ]);

        $updateData = [
            'title' => 'Unauthorized Update',
            'content' => 'This should not work'
        ];

        $response = $this->put("/superadmin/announcements/{$announcement->id}", $updateData);
        
        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function content_deletion_requires_proper_authorization()
    {
        $this->actingAs($this->user); // Regular user
        
        $announcement = Announcement::factory()->create([
            'admin_id' => $this->superAdmin->id
        ]);

        $response = $this->delete("/superadmin/announcements/{$announcement->id}");
        
        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function file_upload_validation_prevents_dangerous_files()
    {
        $this->actingAs($this->superAdmin, 'admin');

        $response = $this->post('/superadmin/news', [
            'title' => 'Test News',
            'content' => 'Test content',
            'is_published' => true,
            'media_files' => [
                'malicious.php',
                'script.js',
                'virus.exe'
            ]
        ]);

        // Should reject dangerous file types
        $response->assertSessionHasErrors(['media_files']);
    }

    /** @test */
    public function file_upload_size_limits_are_enforced()
    {
        $this->actingAs($this->superAdmin, 'admin');

        // Simulate large file upload
        $response = $this->post('/superadmin/news', [
            'title' => 'Test News',
            'content' => 'Test content',
            'is_published' => true,
            'media_files' => [
                'large_file.jpg' // Assuming this exceeds size limit
            ]
        ]);

        // Should reject files that are too large
        $response->assertSessionHasErrors(['media_files']);
    }

    /** @test */
    public function session_security_is_maintained()
    {
        $this->actingAs($this->user);

        $response = $this->get('/user/dashboard');
        
        $response->assertStatus(200);
        
        // Check that session is properly maintained
        $this->assertAuthenticated();
    }

    /** @test */
    public function logout_invalidates_session()
    {
        $this->actingAs($this->user);

        $response = $this->post('/logout');
        
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    /** @test */
    public function password_reset_tokens_are_secure()
    {
        $email = 'test@student.mcc.edu.ph';
        
        // Create password reset record
        $token = 'secure-token';
        \DB::table('password_resets')->insert([
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => now()
        ]);

        // Try with wrong token
        $response = $this->post('/reset-password', [
            'token' => 'wrong-token',
            'email' => $email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function password_reset_tokens_expire()
    {
        $email = 'test@student.mcc.edu.ph';
        
        // Create expired password reset record
        $token = 'expired-token';
        \DB::table('password_resets')->insert([
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => now()->subHours(2) // 2 hours ago
        ]);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function registration_tokens_are_secure()
    {
        $email = 'test@student.mcc.edu.ph';
        
        // Try to access registration form without valid token
        $response = $this->get('/register?email=' . urlencode($email));
        
        $response->assertStatus(401); // Unauthorized
    }

    /** @test */
    public function registration_tokens_expire()
    {
        $email = 'test@student.mcc.edu.ph';
        
        // Create expired registration URL
        $url = URL::temporarySignedRoute(
            'ms365.register.form',
            now()->subMinutes(35), // 35 minutes ago (expired)
            ['email' => $email]
        );

        $response = $this->get($url);
        
        $response->assertStatus(401); // Unauthorized
    }

    /** @test */
    public function input_sanitization_prevents_malicious_content()
    {
        $this->actingAs($this->superAdmin, 'admin');

        $maliciousContent = '<script>alert("XSS")</script><img src="x" onerror="alert(\'XSS\')">';

        $response = $this->post('/superadmin/announcements', [
            'title' => 'Test Announcement',
            'content' => $maliciousContent,
            'is_published' => true
        ]);

        $response->assertRedirect('/superadmin/announcements');
        
        // Check that malicious content is sanitized in database
        $announcement = Announcement::where('title', 'Test Announcement')->first();
        $this->assertStringNotContainsString('<script>', $announcement->content);
        $this->assertStringNotContainsString('onerror=', $announcement->content);
    }

    /** @test */
    public function rate_limiting_prevents_brute_force_attacks()
    {
        // Attempt multiple failed logins
        for ($i = 0; $i < 10; $i++) {
            $response = $this->post('/login', [
                'login_type' => 'ms365',
                'ms365_account' => 'test@student.mcc.edu.ph',
                'password' => 'wrongpassword'
            ]);
        }

        // Should be rate limited
        $response->assertStatus(429); // Too Many Requests
    }

    /** @test */
    public function sensitive_data_is_not_exposed_in_responses()
    {
        $this->actingAs($this->user);

        $response = $this->get('/user/dashboard');

        $response->assertStatus(200);
        
        // Check that sensitive data is not exposed
        $response->assertDontSee($this->user->password);
        $response->assertDontSee('password');
        $response->assertDontSee('token');
    }
}
