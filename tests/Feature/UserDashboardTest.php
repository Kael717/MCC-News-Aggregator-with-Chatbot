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
use Carbon\Carbon;

class UserDashboardTest extends TestCase
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
    public function user_can_access_dashboard()
    {
        $this->actingAs($this->user);

        $response = $this->get('/user/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('user.dashboard');
    }

    /** @test */
    public function dashboard_shows_user_specific_content()
    {
        $this->actingAs($this->user);

        // Create content visible to user's department
        Announcement::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'title' => 'General Announcement',
            'is_published' => true,
            'target_departments' => ['Bachelor of Science in Information Technology']
        ]);

        Event::factory()->create([
            'admin_id' => $this->departmentAdmin->id,
            'title' => 'BSIT Department Event',
            'is_published' => true,
            'event_date' => Carbon::now()->addDays(7)
        ]);

        $response = $this->get('/user/dashboard');

        $response->assertStatus(200);
        $response->assertSee('General Announcement');
        $response->assertSee('BSIT Department Event');
    }

    /** @test */
    public function dashboard_shows_correct_content_counts()
    {
        $this->actingAs($this->user);

        // Create multiple pieces of content
        Announcement::factory()->count(3)->create([
            'admin_id' => $this->superAdmin->id,
            'is_published' => true,
            'target_departments' => ['Bachelor of Science in Information Technology']
        ]);

        Event::factory()->count(2)->create([
            'admin_id' => $this->departmentAdmin->id,
            'is_published' => true,
            'event_date' => Carbon::now()->addDays(rand(1, 30))
        ]);

        News::factory()->count(1)->create([
            'admin_id' => $this->superAdmin->id,
            'is_published' => true,
            'target_departments' => ['Bachelor of Science in Information Technology']
        ]);

        $response = $this->get('/user/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('totalAnnouncements', 3);
        $response->assertViewHas('totalEvents', 2);
        $response->assertViewHas('totalNews', 1);
    }

    /** @test */
    public function dashboard_filters_content_by_department()
    {
        $this->actingAs($this->user);

        // Create content for different departments
        Announcement::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'title' => 'BSIT Announcement',
            'is_published' => true,
            'target_departments' => ['Bachelor of Science in Information Technology']
        ]);

        Announcement::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'title' => 'BSBA Announcement',
            'is_published' => true,
            'target_departments' => ['Bachelor of Science in Business Administration']
        ]);

        $response = $this->get('/user/dashboard');

        $response->assertStatus(200);
        $response->assertSee('BSIT Announcement');
        $response->assertDontSee('BSBA Announcement');
    }

    /** @test */
    public function dashboard_shows_recent_events()
    {
        $this->actingAs($this->user);

        // Create recent event
        Event::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'title' => 'Recent Event',
            'is_published' => true,
            'event_date' => Carbon::now()->addDays(3)
        ]);

        // Create old event (more than 30 days)
        Event::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'title' => 'Old Event',
            'is_published' => true,
            'event_date' => Carbon::now()->subDays(45)
        ]);

        $response = $this->get('/user/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Recent Event');
        $response->assertDontSee('Old Event');
    }

    /** @test */
    public function dashboard_shows_past_events_within_30_days()
    {
        $this->actingAs($this->user);

        // Create recent past event
        Event::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'title' => 'Recent Past Event',
            'is_published' => true,
            'event_date' => Carbon::now()->subDays(15)
        ]);

        $response = $this->get('/user/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Recent Past Event');
    }

    /** @test */
    public function dashboard_shows_tbd_events()
    {
        $this->actingAs($this->user);

        Event::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'title' => 'TBD Event',
            'is_published' => true,
            'event_date' => null
        ]);

        $response = $this->get('/user/dashboard');

        $response->assertStatus(200);
        $response->assertSee('TBD Event');
        $response->assertSee('Date TBD');
    }

    /** @test */
    public function user_can_view_announcement_details()
    {
        $this->actingAs($this->user);

        $announcement = Announcement::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'title' => 'Test Announcement',
            'content' => 'This is the announcement content.',
            'is_published' => true,
            'target_departments' => ['Bachelor of Science in Information Technology']
        ]);

        $response = $this->get("/user/content/announcement/{$announcement->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'title' => 'Test Announcement',
            'content' => 'This is the announcement content.'
        ]);
    }

    /** @test */
    public function user_can_view_event_details()
    {
        $this->actingAs($this->user);

        $event = Event::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'title' => 'Test Event',
            'description' => 'This is the event description.',
            'is_published' => true,
            'event_date' => Carbon::now()->addDays(7)
        ]);

        $response = $this->get("/user/content/event/{$event->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'title' => 'Test Event',
            'description' => 'This is the event description.'
        ]);
    }

    /** @test */
    public function user_can_view_news_details()
    {
        $this->actingAs($this->user);

        $news = News::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'title' => 'Test News',
            'content' => 'This is the news content.',
            'is_published' => true,
            'target_departments' => ['Bachelor of Science in Information Technology']
        ]);

        $response = $this->get("/user/content/news/{$news->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'title' => 'Test News',
            'content' => 'This is the news content.'
        ]);
    }

    /** @test */
    public function user_can_add_comments_to_content()
    {
        $this->actingAs($this->user);

        $announcement = Announcement::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'is_published' => true,
            'target_departments' => ['Bachelor of Science in Information Technology']
        ]);

        $commentData = [
            'content' => 'This is a test comment.',
            'commentable_type' => 'App\\Models\\Announcement',
            'commentable_id' => $announcement->id
        ];

        $response = $this->post('/user/comments', $commentData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('comments', [
            'user_id' => $this->user->id,
            'content' => 'This is a test comment.',
            'commentable_type' => 'App\\Models\\Announcement',
            'commentable_id' => $announcement->id
        ]);
    }

    /** @test */
    public function user_can_view_comments_for_content()
    {
        $this->actingAs($this->user);

        $announcement = Announcement::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'is_published' => true,
            'target_departments' => ['Bachelor of Science in Information Technology']
        ]);

        // Create comments
        Comment::factory()->count(3)->create([
            'commentable_type' => 'App\\Models\\Announcement',
            'commentable_id' => $announcement->id,
            'user_id' => $this->user->id
        ]);

        $response = $this->get("/user/content/announcement/{$announcement->id}/comments");

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'comments');
    }

    /** @test */
    public function user_can_update_their_own_comments()
    {
        $this->actingAs($this->user);

        $announcement = Announcement::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'is_published' => true,
            'target_departments' => ['Bachelor of Science in Information Technology']
        ]);

        $comment = Comment::factory()->create([
            'user_id' => $this->user->id,
            'commentable_type' => 'App\\Models\\Announcement',
            'commentable_id' => $announcement->id,
            'content' => 'Original comment'
        ]);

        $updateData = [
            'content' => 'Updated comment'
        ];

        $response = $this->put("/user/comments/{$comment->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Updated comment'
        ]);
    }

    /** @test */
    public function user_can_delete_their_own_comments()
    {
        $this->actingAs($this->user);

        $announcement = Announcement::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'is_published' => true,
            'target_departments' => ['Bachelor of Science in Information Technology']
        ]);

        $comment = Comment::factory()->create([
            'user_id' => $this->user->id,
            'commentable_type' => 'App\\Models\\Announcement',
            'commentable_id' => $announcement->id
        ]);

        $response = $this->delete("/user/comments/{$comment->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    /** @test */
    public function user_cannot_access_other_department_content()
    {
        $this->actingAs($this->user);

        $announcement = Announcement::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'is_published' => true,
            'target_departments' => ['Bachelor of Science in Business Administration']
        ]);

        $response = $this->get("/user/content/announcement/{$announcement->id}");

        $response->assertStatus(403); // Should be forbidden
    }

    /** @test */
    public function dashboard_requires_authentication()
    {
        $response = $this->get('/user/dashboard');

        $response->assertRedirect('/login');
    }

    /** @test */
    public function dashboard_shows_user_profile_information()
    {
        $this->actingAs($this->user);

        $response = $this->get('/user/dashboard');

        $response->assertStatus(200);
        $response->assertSee($this->user->first_name);
        $response->assertSee($this->user->surname);
        $response->assertSee($this->user->department);
        $response->assertSee($this->user->year_level);
    }
}
