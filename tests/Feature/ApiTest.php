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
use Illuminate\Support\Facades\Http;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $superAdmin;

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
    }

    /** @test */
    public function chatbot_api_responds_to_requests()
    {
        $this->actingAs($this->user);

        $response = $this->post('/api/chatbot', [
            'message' => 'Hello, how are you?'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'response'
        ]);
    }

    /** @test */
    public function chatbot_api_requires_authentication()
    {
        $response = $this->post('/api/chatbot', [
            'message' => 'Hello, how are you?'
        ]);

        $response->assertStatus(401); // Unauthorized
    }

    /** @test */
    public function chatbot_api_handles_empty_messages()
    {
        $this->actingAs($this->user);

        $response = $this->post('/api/chatbot', [
            'message' => ''
        ]);

        $response->assertStatus(400); // Bad request
    }

    /** @test */
    public function chatbot_api_handles_long_messages()
    {
        $this->actingAs($this->user);

        $longMessage = str_repeat('This is a very long message. ', 100);

        $response = $this->post('/api/chatbot', [
            'message' => $longMessage
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function comments_api_returns_comments_for_content()
    {
        $this->actingAs($this->user);

        $announcement = Announcement::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'is_published' => true,
            'target_departments' => ['Bachelor of Science in Information Technology']
        ]);

        // Create test comments
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
    public function comments_api_creates_new_comment()
    {
        $this->actingAs($this->user);

        $announcement = Announcement::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'is_published' => true,
            'target_departments' => ['Bachelor of Science in Information Technology']
        ]);

        $commentData = [
            'content' => 'This is a new comment',
            'commentable_type' => 'App\\Models\\Announcement',
            'commentable_id' => $announcement->id
        ];

        $response = $this->post('/user/comments', $commentData);

        $response->assertStatus(201);
        $response->assertJson([
            'content' => 'This is a new comment',
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function comments_api_updates_existing_comment()
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
            'content' => 'Updated comment content'
        ];

        $response = $this->put("/user/comments/{$comment->id}", $updateData);

        $response->assertStatus(200);
        $response->assertJson([
            'content' => 'Updated comment content'
        ]);
    }

    /** @test */
    public function comments_api_deletes_comment()
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
    public function notifications_api_returns_user_notifications()
    {
        $this->actingAs($this->user);

        $response = $this->get('/user/notifications');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'notifications'
        ]);
    }

    /** @test */
    public function notifications_api_marks_notification_as_read()
    {
        $this->actingAs($this->user);

        // Create a test notification (assuming you have a notifications table)
        $notification = \DB::table('notifications')->insertGetId([
            'user_id' => $this->user->id,
            'title' => 'Test Notification',
            'message' => 'This is a test notification',
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $response = $this->post("/user/notifications/{$notification}/read");

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('notifications', [
            'id' => $notification,
            'read_at' => now()->format('Y-m-d H:i:s')
        ]);
    }

    /** @test */
    public function notifications_api_marks_all_notifications_as_read()
    {
        $this->actingAs($this->user);

        // Create multiple test notifications
        \DB::table('notifications')->insert([
            [
                'user_id' => $this->user->id,
                'title' => 'Notification 1',
                'message' => 'First notification',
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'user_id' => $this->user->id,
                'title' => 'Notification 2',
                'message' => 'Second notification',
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        $response = $this->post('/user/notifications/mark-all-read');

        $response->assertStatus(200);
        
        $unreadCount = \DB::table('notifications')
            ->where('user_id', $this->user->id)
            ->whereNull('read_at')
            ->count();
            
        $this->assertEquals(0, $unreadCount);
    }

    /** @test */
    public function notifications_api_returns_unread_count()
    {
        $this->actingAs($this->user);

        // Create test notifications
        \DB::table('notifications')->insert([
            [
                'user_id' => $this->user->id,
                'title' => 'Unread Notification',
                'message' => 'This is unread',
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'user_id' => $this->user->id,
                'title' => 'Read Notification',
                'message' => 'This is read',
                'read_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        $response = $this->get('/user/notifications/unread-count');

        $response->assertStatus(200);
        $response->assertJson([
            'unread_count' => 1
        ]);
    }

    /** @test */
    public function notifications_api_deletes_notification()
    {
        $this->actingAs($this->user);

        $notification = \DB::table('notifications')->insertGetId([
            'user_id' => $this->user->id,
            'title' => 'Test Notification',
            'message' => 'This notification will be deleted',
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $response = $this->delete("/user/notifications/{$notification}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('notifications', ['id' => $notification]);
    }

    /** @test */
    public function content_api_returns_announcement_data()
    {
        $this->actingAs($this->user);

        $announcement = Announcement::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'title' => 'Test Announcement',
            'content' => 'Test content',
            'is_published' => true,
            'target_departments' => ['Bachelor of Science in Information Technology']
        ]);

        $response = $this->get("/user/content/announcement/{$announcement->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $announcement->id,
            'title' => 'Test Announcement',
            'content' => 'Test content'
        ]);
    }

    /** @test */
    public function content_api_returns_event_data()
    {
        $this->actingAs($this->user);

        $event = Event::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'title' => 'Test Event',
            'description' => 'Test event description',
            'is_published' => true,
            'event_date' => now()->addDays(7)
        ]);

        $response = $this->get("/user/content/event/{$event->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $event->id,
            'title' => 'Test Event',
            'description' => 'Test event description'
        ]);
    }

    /** @test */
    public function content_api_returns_news_data()
    {
        $this->actingAs($this->user);

        $news = News::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'title' => 'Test News',
            'content' => 'Test news content',
            'is_published' => true,
            'target_departments' => ['Bachelor of Science in Information Technology']
        ]);

        $response = $this->get("/user/content/news/{$news->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $news->id,
            'title' => 'Test News',
            'content' => 'Test news content'
        ]);
    }

    /** @test */
    public function api_handles_cors_headers()
    {
        $response = $this->options('/api/chatbot');

        $response->assertStatus(200);
        $response->assertHeader('Access-Control-Allow-Origin');
        $response->assertHeader('Access-Control-Allow-Methods');
        $response->assertHeader('Access-Control-Allow-Headers');
    }

    /** @test */
    public function api_handles_rate_limiting()
    {
        $this->actingAs($this->user);

        // Make multiple requests to test rate limiting
        for ($i = 0; $i < 10; $i++) {
            $response = $this->post('/api/chatbot', [
                'message' => "Test message {$i}"
            ]);
            
            if ($i < 5) {
                $response->assertStatus(200);
            } else {
                // After rate limit, should return 429
                $response->assertStatus(429);
            }
        }
    }

    /** @test */
    public function api_validates_input_data()
    {
        $this->actingAs($this->user);

        $announcement = Announcement::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'is_published' => true,
            'target_departments' => ['Bachelor of Science in Information Technology']
        ]);

        // Test with invalid comment data
        $response = $this->post('/user/comments', [
            'content' => '', // Empty content
            'commentable_type' => 'App\\Models\\Announcement',
            'commentable_id' => $announcement->id
        ]);

        $response->assertStatus(422); // Validation error
    }

    /** @test */
    public function api_handles_missing_content()
    {
        $this->actingAs($this->user);

        $response = $this->get('/user/content/announcement/999999');

        $response->assertStatus(404);
    }

    /** @test */
    public function api_handles_unauthorized_access()
    {
        $this->actingAs($this->user);

        // Create content for different department
        $announcement = Announcement::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'is_published' => true,
            'target_departments' => ['Bachelor of Science in Business Administration']
        ]);

        $response = $this->get("/user/content/announcement/{$announcement->id}");

        $response->assertStatus(403); // Forbidden
    }
}
