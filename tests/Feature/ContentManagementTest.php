<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class ContentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;
    protected $departmentAdmin;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->superAdmin = Admin::factory()->create([
            'role' => 'super_admin',
            'department' => 'Administration'
        ]);

        $this->departmentAdmin = Admin::factory()->create([
            'role' => 'department_admin',
            'department' => 'Bachelor of Science in Information Technology'
        ]);

        $this->user = User::factory()->create([
            'role' => 'student',
            'department' => 'Bachelor of Science in Information Technology'
        ]);
    }

    /** @test */
    public function superadmin_can_create_announcement()
    {
        $this->actingAs($this->superAdmin, 'admin');

        $announcementData = [
            'title' => 'Important Announcement',
            'content' => 'This is an important announcement for all students.',
            'is_published' => true,
            'target_departments' => ['Bachelor of Science in Information Technology']
        ];

        $response = $this->post('/superadmin/announcements', $announcementData);

        $response->assertRedirect('/superadmin/announcements');
        $this->assertDatabaseHas('announcements', [
            'title' => 'Important Announcement',
            'admin_id' => $this->superAdmin->id,
            'is_published' => true
        ]);
    }

    /** @test */
    public function department_admin_can_create_announcement_for_their_department()
    {
        $this->actingAs($this->departmentAdmin, 'admin');

        $announcementData = [
            'title' => 'BSIT Department Announcement',
            'content' => 'This is a BSIT-specific announcement.',
            'is_published' => true
        ];

        $response = $this->post('/department-admin/announcements', $announcementData);

        $response->assertRedirect('/department-admin/announcements');
        $this->assertDatabaseHas('announcements', [
            'title' => 'BSIT Department Announcement',
            'admin_id' => $this->departmentAdmin->id,
            'is_published' => true
        ]);
    }

    /** @test */
    public function superadmin_can_create_event()
    {
        $this->actingAs($this->superAdmin, 'admin');

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
        $this->assertDatabaseHas('events', [
            'title' => 'MCC Foundation Week',
            'admin_id' => $this->superAdmin->id,
            'is_published' => true
        ]);
    }

    /** @test */
    public function superadmin_can_create_news_article()
    {
        $this->actingAs($this->superAdmin, 'admin');

        $newsData = [
            'title' => 'MCC Achieves New Milestone',
            'content' => 'MCC has achieved a new milestone in education.',
            'is_published' => true,
            'target_departments' => ['Bachelor of Science in Information Technology']
        ];

        $response = $this->post('/superadmin/news', $newsData);

        $response->assertRedirect('/superadmin/news');
        $this->assertDatabaseHas('news', [
            'title' => 'MCC Achieves New Milestone',
            'admin_id' => $this->superAdmin->id,
            'is_published' => true
        ]);
    }

    /** @test */
    public function superadmin_can_edit_announcement()
    {
        $this->actingAs($this->superAdmin, 'admin');

        $announcement = Announcement::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'title' => 'Original Title'
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'is_published' => true
        ];

        $response = $this->put("/superadmin/announcements/{$announcement->id}", $updateData);

        $response->assertRedirect('/superadmin/announcements');
        $this->assertDatabaseHas('announcements', [
            'id' => $announcement->id,
            'title' => 'Updated Title',
            'content' => 'Updated content'
        ]);
    }

    /** @test */
    public function superadmin_can_delete_announcement()
    {
        $this->actingAs($this->superAdmin, 'admin');

        $announcement = Announcement::factory()->create([
            'admin_id' => $this->superAdmin->id
        ]);

        $response = $this->delete("/superadmin/announcements/{$announcement->id}");

        $response->assertRedirect('/superadmin/announcements');
        $this->assertDatabaseMissing('announcements', ['id' => $announcement->id]);
    }

    /** @test */
    public function department_admin_cannot_edit_other_department_content()
    {
        $this->actingAs($this->departmentAdmin, 'admin');

        // Create announcement by superadmin
        $announcement = Announcement::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'title' => 'SuperAdmin Announcement'
        ]);

        $updateData = [
            'title' => 'Unauthorized Update',
            'content' => 'This should not work',
            'is_published' => true
        ];

        $response = $this->put("/department-admin/announcements/{$announcement->id}", $updateData);

        // Should redirect with error or not update
        $response->assertStatus(403); // or appropriate error status
    }

    /** @test */
    public function unpublished_content_is_not_visible_to_users()
    {
        $this->actingAs($this->user);

        // Create unpublished announcement
        Announcement::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'title' => 'Unpublished Announcement',
            'is_published' => false
        ]);

        $response = $this->get('/user/dashboard');

        $response->assertStatus(200);
        $response->assertDontSee('Unpublished Announcement');
    }

    /** @test */
    public function published_content_is_visible_to_users()
    {
        $this->actingAs($this->user);

        // Create published announcement
        Announcement::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'title' => 'Published Announcement',
            'is_published' => true
        ]);

        $response = $this->get('/user/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Published Announcement');
    }

    /** @test */
    public function events_show_correct_date_formatting()
    {
        $this->actingAs($this->user);

        $eventDate = Carbon::now()->addDays(5);
        Event::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'title' => 'Future Event',
            'event_date' => $eventDate,
            'is_published' => true
        ]);

        $response = $this->get('/user/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Future Event');
        $response->assertSee($eventDate->format('M d, Y')); // Check date formatting
    }

    /** @test */
    public function tbd_events_show_appropriate_message()
    {
        $this->actingAs($this->user);

        Event::factory()->create([
            'admin_id' => $this->superAdmin->id,
            'title' => 'TBD Event',
            'event_date' => null,
            'is_published' => true
        ]);

        $response = $this->get('/user/dashboard');

        $response->assertStatus(200);
        $response->assertSee('TBD Event');
        $response->assertSee('Date TBD');
    }

    /** @test */
    public function content_has_proper_media_handling()
    {
        $this->actingAs($this->superAdmin, 'admin');

        $newsData = [
            'title' => 'News with Media',
            'content' => 'This news article has media attachments.',
            'is_published' => true,
            'media_files' => [
                'test-image.jpg',
                'test-document.pdf'
            ]
        ];

        $response = $this->post('/superadmin/news', $newsData);

        $response->assertRedirect('/superadmin/news');
        $this->assertDatabaseHas('news', [
            'title' => 'News with Media',
            'admin_id' => $this->superAdmin->id
        ]);
    }

    /** @test */
    public function content_creation_requires_authentication()
    {
        $announcementData = [
            'title' => 'Unauthorized Announcement',
            'content' => 'This should not work',
            'is_published' => true
        ];

        $response = $this->post('/superadmin/announcements', $announcementData);

        $response->assertRedirect('/login'); // Should redirect to login
    }

    /** @test */
    public function content_editing_requires_authentication()
    {
        $announcement = Announcement::factory()->create([
            'admin_id' => $this->superAdmin->id
        ]);

        $updateData = [
            'title' => 'Unauthorized Update',
            'content' => 'This should not work'
        ];

        $response = $this->put("/superadmin/announcements/{$announcement->id}", $updateData);

        $response->assertRedirect('/login'); // Should redirect to login
    }

    /** @test */
    public function content_deletion_requires_authentication()
    {
        $announcement = Announcement::factory()->create([
            'admin_id' => $this->superAdmin->id
        ]);

        $response = $this->delete("/superadmin/announcements/{$announcement->id}");

        $response->assertRedirect('/login'); // Should redirect to login
    }
}
