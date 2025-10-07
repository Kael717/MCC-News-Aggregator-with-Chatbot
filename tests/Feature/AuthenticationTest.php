<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Ms365Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users
        $this->user = User::factory()->create([
            'ms365_account' => 'test@student.mcc.edu.ph',
            'role' => 'student',
            'department' => 'Bachelor of Science in Information Technology',
            'year_level' => '3rd Year'
        ]);

        $this->admin = Admin::factory()->create([
            'username' => 'testadmin',
            'role' => 'department_admin',
            'department' => 'Bachelor of Science in Information Technology'
        ]);

        $this->superAdmin = Admin::factory()->create([
            'username' => 'superadmin',
            'role' => 'super_admin',
            'department' => 'Administration'
        ]);
    }

    /** @test */
    public function user_can_view_unified_login_form()
    {
        $response = $this->get('/login');
        
        $response->assertStatus(200);
        $response->assertViewIs('auth.unified-login');
        $response->assertSee('Login - MCC News Aggregator');
    }

    /** @test */
    public function user_can_login_with_ms365_credentials()
    {
        $response = $this->post('/login', [
            'login_type' => 'ms365',
            'ms365_account' => 'test@student.mcc.edu.ph',
            'password' => 'password'
        ]);

        $response->assertRedirect('/user/dashboard');
        $this->assertAuthenticated();
    }

    /** @test */
    public function user_can_login_with_gmail_credentials()
    {
        $gmailUser = User::factory()->create([
            'gmail_account' => 'test@gmail.com',
            'role' => 'student',
            'department' => 'Bachelor of Science in Information Technology'
        ]);

        $response = $this->post('/login', [
            'login_type' => 'user',
            'username' => 'test@gmail.com',
            'password' => 'password'
        ]);

        $response->assertRedirect('/user/dashboard');
        $this->assertAuthenticated();
    }

    /** @test */
    public function department_admin_can_login()
    {
        $response = $this->post('/login', [
            'login_type' => 'department-admin',
            'username' => 'testadmin',
            'password' => 'password'
        ]);

        $response->assertRedirect('/department-admin/dashboard');
        $this->assertAuthenticated('admin');
    }

    /** @test */
    public function superadmin_can_login()
    {
        $response = $this->post('/login', [
            'login_type' => 'superadmin',
            'username' => 'superadmin',
            'password' => 'password'
        ]);

        $response->assertRedirect('/superadmin/dashboard');
        $this->assertAuthenticated('admin');
    }

    /** @test */
    public function invalid_credentials_are_rejected()
    {
        $response = $this->post('/login', [
            'login_type' => 'ms365',
            'ms365_account' => 'test@student.mcc.edu.ph',
            'password' => 'wrongpassword'
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /** @test */
    public function user_can_logout()
    {
        $this->actingAs($this->user);
        
        $response = $this->post('/logout');
        
        $response->assertRedirect('/login');
        $response->assertSessionHas('success', 'You have been logged out successfully.');
        $this->assertGuest();
    }

    /** @test */
    public function user_can_register_with_ms365_account()
    {
        Mail::fake();

        $response = $this->post('/signup', [
            'ms365_account' => 'newuser@student.mcc.edu.ph'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Registration link sent to your email.');
        
        Mail::assertSent(\Illuminate\Mail\Mailable::class);
    }

    /** @test */
    public function user_can_complete_registration_with_valid_token()
    {
        $email = 'newuser@student.mcc.edu.ph';
        $url = URL::temporarySignedRoute(
            'ms365.register.form',
            now()->addMinutes(30),
            ['email' => $email]
        );

        $response = $this->get($url);
        
        $response->assertStatus(200);
        $response->assertViewIs('auth.ms365-register');
        $response->assertViewHas('email', $email);
    }

    /** @test */
    public function user_can_complete_registration_process()
    {
        $email = 'newuser@student.mcc.edu.ph';
        
        $response = $this->post('/ms365/register', [
            'email' => $email,
            'first_name' => 'John',
            'middle_name' => 'Michael',
            'surname' => 'Doe',
            'role' => 'student',
            'department' => 'Bachelor of Science in Information Technology',
            'year_level' => '1st Year',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertRedirect('/user/dashboard');
        $response->assertSessionHas('status', 'Registration successful! Welcome to MCC News Aggregator.');
        
        $this->assertDatabaseHas('users', [
            'ms365_account' => $email,
            'first_name' => 'John',
            'surname' => 'Doe',
            'role' => 'student'
        ]);
        
        $this->assertAuthenticated();
    }

    /** @test */
    public function password_reset_flow_works()
    {
        Mail::fake();

        $response = $this->post('/forgot-password', [
            'ms365_account' => 'test@student.mcc.edu.ph'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Password reset link has been sent to your MS365 email account.');
        
        Mail::assertSent(\Illuminate\Mail\Mailable::class);
    }

    /** @test */
    public function user_can_reset_password_with_valid_token()
    {
        $token = 'valid-token';
        $email = 'test@student.mcc.edu.ph';
        
        // Create password reset record
        \DB::table('password_resets')->insert([
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => now()
        ]);

        $response = $this->get(route('password.reset', ['token' => $token]) . '?email=' . urlencode($email));
        
        $response->assertStatus(200);
        $response->assertViewIs('auth.reset-password');
        $response->assertViewHas('token', $token);
        $response->assertViewHas('email', $email);
    }

    /** @test */
    public function user_can_update_password_with_valid_token()
    {
        $token = 'valid-token';
        $email = 'test@student.mcc.edu.ph';
        
        // Create password reset record
        \DB::table('password_resets')->insert([
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => now()
        ]);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('success', 'Your password has been successfully reset. You can now log in with your new password.');
        
        // Verify password was updated
        $user = User::where('ms365_account', $email)->first();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
        
        // Verify reset token was deleted
        $this->assertDatabaseMissing('password_resets', ['email' => $email]);
    }

    /** @test */
    public function registration_requires_valid_department_for_students()
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
    public function registration_requires_year_level_for_students()
    {
        $response = $this->post('/ms365/register', [
            'email' => 'test@student.mcc.edu.ph',
            'first_name' => 'John',
            'surname' => 'Doe',
            'role' => 'student',
            'department' => 'Bachelor of Science in Information Technology',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertSessionHasErrors(['year_level']);
    }

    /** @test */
    public function faculty_registration_does_not_require_year_level()
    {
        $response = $this->post('/ms365/register', [
            'email' => 'faculty@mcc.edu.ph',
            'first_name' => 'Jane',
            'surname' => 'Smith',
            'role' => 'faculty',
            'department' => 'Bachelor of Science in Information Technology',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertRedirect('/user/dashboard');
        
        $this->assertDatabaseHas('users', [
            'ms365_account' => 'faculty@mcc.edu.ph',
            'role' => 'faculty',
            'year_level' => null
        ]);
    }
}
