<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\News;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class IntegrationTest extends TestCase
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
            'department' => 'Bachelor of Science in Information Technology',
            'year_level' => '3rd Year'
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
    public function complete_user_registration_and_login_flow()
    {
        Mail::fake();

        // Step 1: User requests registration
        $response = $this->post('/signup', [
            'ms365_account' => 'newuser@student.mcc.edu.ph'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Registration link sent to your email.');

        // Step 2: User clicks registration link
        $email = 'newuser@student.mcc.edu.ph';
        $url = URL::temporarySignedRoute(
            'ms365.register.form',
            now()->addMinutes(30),
            ['email' => $email]
        );

        $response = $this->get($url);
        $response->assertStatus(200);

        // Step 3: User completes registration
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
        $this->assertAuthenticated();

        // Step 4: User logs out
        $response = $this->post('/logout');
        $response->assertRedirect('/login');
        $this->assertGuest();

        // Step 5: User logs back in
        $response = $this->post('/login', [
            'login_type' => 'ms365',
            'ms365_account' => $email,
            'password' => 'password123'
        ]);

        $response->assertRedirect('/user/dashboard');
        $this->assertAuthenticated();
    }

    /** @test */
    public function complete_content_creation_and_consumption_flow()
    {
        // Step 1: SuperAdmin creates announcement
        $this->actingAs($this->superAdmin, 'admin');

        $announcementData = [
            'title' => 'Important Announcement',
            'content' => 'This is an important announcement for all students.',
            'is_published' => true,
            'target_departments' => ['Bachelor of Science in Information Technology']
        ];

        $response = $this->post('/superadmin/announcements', $announcementData);
        $response->assertRedirect('/superadmin/announcements');

        // Step 2: SuperAdmin creates event
        $eventData = [
            'title' => 'MCC Foundation Week',
            'description' => 'Annual foundation week celebration',
            'event_date' => Carbon::now()->addDays(7)->format('Y-m-d H:i:s'),
            'location' => 'MCC Main Campus',
            'is_published' => true,
            'target_departments' => ['Bachelor of Science in Information Technology']
        ];

        $response = $this->post('/superadmin/events', $eventData);
        $response->assertRedirect('/superadmin/events');

        // Step 3: SuperAdmin creates news article
        $newsData = [
            'title' => 'MCC Achieves New Milestone',
            'content' => 'MCC has achieved a new milestone in education.',
            'is_published' => true,
            'target_departments' => ['Bachelor of Science in Information Technology']
        ];

        $response = $this->post('/superadmin/news', $newsData);
        $response->assertRedirect('/superadmin/news');

        // Step 4: User views dashboard and sees content
        $this->actingAs($this->user);

        $response = $this->get('/user/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Important Announcement');
        $response->assertSee('MCC Foundation Week');
        $response->assertSee('MCC Achieves New Milestone');

        // Step 5: User views announcement details
        $announcement = Announcement::where('title', 'Important Announcement')->first();
        $response = $this->get("/user/content/announcement/{$announcement->id}");
        $response->assertStatus(200);
        $response->assertJson(['title' => 'Important Announcement']);

        // Step 6: User adds comment to announcement
        $commentData = [
            'content' => 'This is a great announcement!',
            'commentable_type' => 'App\\Models\\Announcement',
            'commentable_id' => $announcement->id
        ];

        $response = $this->post('/user/comments', $commentData);
        $response->assertStatus(201);

        // Step 7: User views comments
        $response = $this->get("/user/content/announcement/{$announcement->id}/comments");
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'comments');
    }

    /** @test */
    public function complete_admin_management_flow()
    {
        // Step 1: SuperAdmin creates department admin
        $this->actingAs($this->superAdmin, 'admin');

        $deptAdminData = [
            'username' => 'newdeptadmin',
            'email' => 'deptadmin@mcc.edu.ph',
            'name' => 'Department Admin',
            'department' => 'Bachelor of Science in Business Administration',
            'role' => 'department_admin'
        ];

        $response = $this->post('/superadmin/department-admins', $deptAdminData);
        $response->assertRedirect('/superadmin/department-admins');

        // Step 2: Department admin logs in
        $deptAdmin = Admin::where('username', 'newdeptadmin')->first();
        $this->actingAs($deptAdmin, 'admin');

        $response = $this->get('/department-admin/dashboard');
        $response->assertStatus(200);

        // Step 3: Department admin creates content
        $announcementData = [
            'title' => 'BSBA Department Announcement',
            'content' => 'This is a BSBA-specific announcement.',
            'is_published' => true
        ];

        $response = $this->post('/department-admin/announcements', $announcementData);
        $response->assertRedirect('/department-admin/announcements');

        // Step 4: SuperAdmin views all content
        $this->actingAs($this->superAdmin, 'admin');

        $response = $this->get('/superadmin/announcements');
        $response->assertStatus(200);
        $response->assertSee('BSBA Department Announcement');

        // Step 5: SuperAdmin can edit department admin's content
        $announcement = Announcement::where('title', 'BSBA Department Announcement')->first();
        $updateData = [
            'title' => 'Updated BSBA Department Announcement',
            'content' => 'This announcement has been updated by superadmin.',
            'is_published' => true
        ];

        $response = $this->put("/superadmin/announcements/{$announcement->id}", $updateData);
        $response->assertRedirect('/superadmin/announcements');
    }

    /** @test */
    public function complete_notification_flow()
    {
        // Step 1: SuperAdmin creates announcement (triggers notification)
        $this->actingAs($this->superAdmin, 'admin');

        $announcementData = [
            'title' => 'New Announcement',
            'content' => 'This announcement should trigger notifications.',
            'is_published' => true,
            'target_departments' => ['Bachelor of Science in Information Technology']
        ];

        $response = $this->post('/superadmin/announcements', $announcementData);
        $response->assertRedirect('/superadmin/announcements');

        // Step 2: User checks notifications
        $this->actingAs($this->user);

        $response = $this->get('/user/notifications');
        $response->assertStatus(200);

        // Step 3: User marks notification as read
        $notification = \DB::table('notifications')
            ->where('user_id', $this->user->id)
            ->whereNull('read_at')
            ->first();

        if ($notification) {
            $response = $this->post("/user/notifications/{$notification->id}/read");
            $response->assertStatus(200);
        }

        // Step 4: User checks unread count
        $response = $this->get('/user/notifications/unread-count');
        $response->assertStatus(200);
        $response->assertJson(['unread_count' => 0]);
    }

    /** @test */
    public function complete_password_reset_flow()
    {
        // Step 1: User requests password reset
        $response = $this->post('/forgot-password', [
            'ms365_account' => $this->user->ms365_account
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Password reset link has been sent to your MS365 email account.');

        // Step 2: User clicks reset link
        $token = 'valid-token';
        $email = $this->user->ms365_account;
        
        \DB::table('password_resets')->insert([
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => now()
        ]);

        $response = $this->get(route('password.reset', ['token' => $token]) . '?email=' . urlencode($email));
        $response->assertStatus(200);

        // Step 3: User resets password
        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('success', 'Your password has been successfully reset. You can now log in with your new password.');

        // Step 4: User logs in with new password
        $response = $this->post('/login', [
            'login_type' => 'ms365',
            'ms365_account' => $email,
            'password' => 'newpassword123'
        ]);

        $response->assertRedirect('/user/dashboard');
        $this->assertAuthenticated();
    }

    /** @test */
    public function complete_comment_interaction_flow()
    {
        // Step 1: SuperAdmin creates announcement
        $this->actingAs($this->superAdmin, 'admin');

        $announcement = Announcement::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'title' => 'Commentable Announcement',
            'is_published' => true,
            'target_departments' => ['Bachelor of Science in Information Technology']
        ]);

        // Step 2: User adds comment
        $this->actingAs($this->user);

        $commentData = [
            'content' => 'This is a test comment',
            'commentable_type' => 'App\\Models\\Announcement',
            'commentable_id' => $announcement->id
        ];

        $response = $this->post('/user/comments', $commentData);
        $response->assertStatus(201);

        // Step 3: User views comments
        $response = $this->get("/user/content/announcement/{$announcement->id}/comments");
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'comments');

        // Step 4: User updates their comment
        $comment = Comment::where('user_id', $this->user->id)->first();
        $updateData = [
            'content' => 'This is an updated comment'
        ];

        $response = $this->put("/user/comments/{$comment->id}", $updateData);
        $response->assertStatus(200);

        // Step 5: User deletes their comment
        $response = $this->delete("/user/comments/{$comment->id}");
        $response->assertStatus(200);

        // Step 6: Verify comment is deleted
        $response = $this->get("/user/content/announcement/{$announcement->id}/comments");
        $response->assertStatus(200);
        $response->assertJsonCount(0, 'comments');
    }

    /** @test */
    public function complete_chatbot_interaction_flow()
    {
        $this->actingAs($this->user);

        // Step 1: User sends message to chatbot
        $response = $this->post('/api/chatbot', [
            'message' => 'Hello, can you help me with information about MCC?'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['response']);

        // Step 2: User asks follow-up question
        $response = $this->post('/api/chatbot', [
            'message' => 'What are the available departments?'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['response']);

        // Step 3: User asks about events
        $response = $this->post('/api/chatbot', [
            'message' => 'Are there any upcoming events?'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['response']);
    }

    /** @test */
    public function complete_department_visibility_flow()
    {
        // Step 1: SuperAdmin creates content for all departments
        $this->actingAs($this->superAdmin, 'admin');

        $generalAnnouncement = Announcement::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'title' => 'General Announcement',
            'is_published' => true,
            'target_departments' => ['Bachelor of Science in Information Technology', 'Bachelor of Science in Business Administration']
        ]);

        // Step 2: Department admin creates content for their department only
        $this->actingAs($this->departmentAdmin, 'admin');

        $deptAnnouncement = Announcement::factory()->create([
            'admin_id' => $this->departmentAdmin->id,
            'title' => 'BSIT Only Announcement',
            'is_published' => true
        ]);

        // Step 3: BSIT user sees both announcements
        $this->actingAs($this->user);

        $response = $this->get('/user/dashboard');
        $response->assertStatus(200);
        $response->assertSee('General Announcement');
        $response->assertSee('BSIT Only Announcement');

        // Step 4: Create BSBA user and verify they only see general announcement
        $bsbaUser = User::factory()->create([
            'role' => 'student',
            'department' => 'Bachelor of Science in Business Administration'
        ]);

        $this->actingAs($bsbaUser);

        $response = $this->get('/user/dashboard');
        $response->assertStatus(200);
        $response->assertSee('General Announcement');
        $response->assertDontSee('BSIT Only Announcement');
    }

    /** @test */
    public function complete_content_lifecycle_flow()
    {
        // Step 1: SuperAdmin creates draft content
        $this->actingAs($this->superAdmin, 'admin');

        $announcementData = [
            'title' => 'Draft Announcement',
            'content' => 'This is a draft announcement.',
            'is_published' => false
        ];

        $response = $this->post('/superadmin/announcements', $announcementData);
        $response->assertRedirect('/superadmin/announcements');

        // Step 2: User cannot see draft content
        $this->actingAs($this->user);

        $response = $this->get('/user/dashboard');
        $response->assertStatus(200);
        $response->assertDontSee('Draft Announcement');

        // Step 3: SuperAdmin publishes content
        $this->actingAs($this->superAdmin, 'admin');

        $announcement = Announcement::where('title', 'Draft Announcement')->first();
        $updateData = [
            'title' => 'Published Announcement',
            'content' => 'This announcement is now published.',
            'is_published' => true
        ];

        $response = $this->put("/superadmin/announcements/{$announcement->id}", $updateData);
        $response->assertRedirect('/superadmin/announcements');

        // Step 4: User can now see published content
        $this->actingAs($this->user);

        $response = $this->get('/user/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Published Announcement');

        // Step 5: SuperAdmin deletes content
        $this->actingAs($this->superAdmin, 'admin');

        $response = $this->delete("/superadmin/announcements/{$announcement->id}");
        $response->assertRedirect('/superadmin/announcements');

        // Step 6: User can no longer see deleted content
        $this->actingAs($this->user);

        $response = $this->get('/user/dashboard');
        $response->assertStatus(200);
        $response->assertDontSee('Published Announcement');
    }
}
