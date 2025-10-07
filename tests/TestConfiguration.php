<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

abstract class TestConfiguration extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up test environment
        $this->setupTestEnvironment();
    }

    protected function setupTestEnvironment()
    {
        // Clear caches
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        // Set test database
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => ':memory:']);

        // Run migrations
        Artisan::call('migrate', ['--database' => 'sqlite']);

        // Seed test data if needed
        $this->seedTestData();
    }

    protected function seedTestData()
    {
        // Create test departments
        $departments = [
            'Bachelor of Science in Information Technology',
            'Bachelor of Science in Business Administration',
            'Bachelor of Elementary Education',
            'Bachelor of Secondary Education',
            'Bachelor of Science in Hospitality Management'
        ];

        // Create test roles
        $roles = ['student', 'faculty'];
        $adminRoles = ['super_admin', 'department_admin', 'office_admin'];

        // Create test year levels
        $yearLevels = ['1st Year', '2nd Year', '3rd Year', '4th Year'];

        // Store in config for use in tests
        config([
            'test.departments' => $departments,
            'test.roles' => $roles,
            'test.admin_roles' => $adminRoles,
            'test.year_levels' => $yearLevels
        ]);
    }

    protected function createTestUser($attributes = [])
    {
        $defaults = [
            'role' => 'student',
            'department' => 'Bachelor of Science in Information Technology',
            'year_level' => '3rd Year',
            'ms365_account' => 'test@student.mcc.edu.ph',
            'password' => bcrypt('password')
        ];

        return \App\Models\User::factory()->create(array_merge($defaults, $attributes));
    }

    protected function createTestAdmin($attributes = [])
    {
        $defaults = [
            'role' => 'department_admin',
            'department' => 'Bachelor of Science in Information Technology',
            'username' => 'testadmin',
            'password' => bcrypt('password')
        ];

        return \App\Models\Admin::factory()->create(array_merge($defaults, $attributes));
    }

    protected function createTestSuperAdmin($attributes = [])
    {
        $defaults = [
            'role' => 'super_admin',
            'department' => 'Administration',
            'username' => 'superadmin',
            'password' => bcrypt('password')
        ];

        return \App\Models\Admin::factory()->create(array_merge($defaults, $attributes));
    }

    protected function createTestAnnouncement($attributes = [])
    {
        $defaults = [
            'title' => 'Test Announcement',
            'content' => 'This is a test announcement.',
            'is_published' => true,
            'target_departments' => ['Bachelor of Science in Information Technology']
        ];

        return \App\Models\Announcement::factory()->create(array_merge($defaults, $attributes));
    }

    protected function createTestEvent($attributes = [])
    {
        $defaults = [
            'title' => 'Test Event',
            'description' => 'This is a test event.',
            'is_published' => true,
            'event_date' => now()->addDays(7),
            'target_departments' => ['Bachelor of Science in Information Technology']
        ];

        return \App\Models\Event::factory()->create(array_merge($defaults, $attributes));
    }

    protected function createTestNews($attributes = [])
    {
        $defaults = [
            'title' => 'Test News',
            'content' => 'This is test news content.',
            'is_published' => true,
            'target_departments' => ['Bachelor of Science in Information Technology']
        ];

        return \App\Models\News::factory()->create(array_merge($defaults, $attributes));
    }

    protected function createTestComment($attributes = [])
    {
        $defaults = [
            'content' => 'This is a test comment.',
            'commentable_type' => 'App\\Models\\Announcement',
            'commentable_id' => 1
        ];

        return \App\Models\Comment::factory()->create(array_merge($defaults, $attributes));
    }

    protected function assertUserCanAccess($user, $route, $expectedStatus = 200)
    {
        $response = $this->actingAs($user)->get($route);
        $response->assertStatus($expectedStatus);
    }

    protected function assertUserCannotAccess($user, $route, $expectedStatus = 403)
    {
        $response = $this->actingAs($user)->get($route);
        $response->assertStatus($expectedStatus);
    }

    protected function assertContentIsVisibleToUser($user, $content, $shouldBeVisible = true)
    {
        $response = $this->actingAs($user)->get('/user/dashboard');
        
        if ($shouldBeVisible) {
            $response->assertSee($content);
        } else {
            $response->assertDontSee($content);
        }
    }

    protected function assertContentIsVisibleToDepartment($department, $content, $shouldBeVisible = true)
    {
        $user = $this->createTestUser(['department' => $department]);
        $this->assertContentIsVisibleToUser($user, $content, $shouldBeVisible);
    }

    protected function assertApiResponse($response, $expectedStatus = 200, $expectedStructure = [])
    {
        $response->assertStatus($expectedStatus);
        
        if (!empty($expectedStructure)) {
            $response->assertJsonStructure($expectedStructure);
        }
    }

    protected function assertDatabaseHasContent($table, $data)
    {
        $this->assertDatabaseHas($table, $data);
    }

    protected function assertDatabaseMissingContent($table, $data)
    {
        $this->assertDatabaseMissing($table, $data);
    }

    protected function assertEmailWasSent($mailable)
    {
        \Illuminate\Support\Facades\Mail::assertSent($mailable);
    }

    protected function assertEmailWasNotSent($mailable)
    {
        \Illuminate\Support\Facades\Mail::assertNotSent($mailable);
    }

    protected function assertNotificationWasSent($notification)
    {
        \Illuminate\Support\Facades\Notification::assertSentTo(
            $this->user,
            $notification
        );
    }

    protected function assertFileWasUploaded($filePath)
    {
        $this->assertTrue(file_exists($filePath));
    }

    protected function assertFileWasNotUploaded($filePath)
    {
        $this->assertFalse(file_exists($filePath));
    }

    protected function assertValidationErrors($response, $fields)
    {
        foreach ($fields as $field) {
            $response->assertSessionHasErrors($field);
        }
    }

    protected function assertNoValidationErrors($response)
    {
        $response->assertSessionHasNoErrors();
    }

    protected function assertRedirectTo($response, $route)
    {
        $response->assertRedirect(route($route));
    }

    protected function assertViewIs($response, $view)
    {
        $response->assertViewIs($view);
    }

    protected function assertViewHas($response, $key, $value = null)
    {
        $response->assertViewHas($key, $value);
    }

    protected function assertJsonResponse($response, $data)
    {
        $response->assertJson($data);
    }

    protected function assertJsonCount($response, $count, $key = null)
    {
        $response->assertJsonCount($count, $key);
    }

    protected function assertJsonStructure($response, $structure)
    {
        $response->assertJsonStructure($structure);
    }

    protected function assertAuthenticated($guard = null)
    {
        $this->assertTrue(auth($guard)->check());
    }

    protected function assertGuest($guard = null)
    {
        $this->assertFalse(auth($guard)->check());
    }

    protected function assertSessionHas($response, $key, $value = null)
    {
        $response->assertSessionHas($key, $value);
    }

    protected function assertSessionMissing($response, $key)
    {
        $response->assertSessionMissing($key);
    }

    protected function assertCookieExists($response, $name)
    {
        $response->assertCookie($name);
    }

    protected function assertCookieMissing($response, $name)
    {
        $response->assertCookieMissing($name);
    }

    protected function assertHeaderExists($response, $name)
    {
        $response->assertHeader($name);
    }

    protected function assertHeaderMissing($response, $name)
    {
        $response->assertHeaderMissing($name);
    }

    protected function assertStatusCode($response, $status)
    {
        $response->assertStatus($status);
    }

    protected function assertResponseIsJson($response)
    {
        $response->assertJson();
    }

    protected function assertResponseIsHtml($response)
    {
        $response->assertHeader('content-type', 'text/html; charset=UTF-8');
    }

    protected function assertResponseIsRedirect($response)
    {
        $response->assertRedirect();
    }

    protected function assertResponseIsSuccessful($response)
    {
        $response->assertSuccessful();
    }

    protected function assertResponseIsClientError($response)
    {
        $response->assertClientError();
    }

    protected function assertResponseIsServerError($response)
    {
        $response->assertServerError();
    }

    protected function assertResponseIsOk($response)
    {
        $response->assertOk();
    }

    protected function assertResponseIsCreated($response)
    {
        $response->assertCreated();
    }

    protected function assertResponseIsAccepted($response)
    {
        $response->assertAccepted();
    }

    protected function assertResponseIsNoContent($response)
    {
        $response->assertNoContent();
    }

    protected function assertResponseIsMovedPermanently($response)
    {
        $response->assertMovedPermanently();
    }

    protected function assertResponseIsFound($response)
    {
        $response->assertFound();
    }

    protected function assertResponseIsSeeOther($response)
    {
        $response->assertSeeOther();
    }

    protected function assertResponseIsNotModified($response)
    {
        $response->assertNotModified();
    }

    protected function assertResponseIsTemporaryRedirect($response)
    {
        $response->assertTemporaryRedirect();
    }

    protected function assertResponseIsPermanentRedirect($response)
    {
        $response->assertPermanentRedirect();
    }

    protected function assertResponseIsUnauthorized($response)
    {
        $response->assertUnauthorized();
    }

    protected function assertResponseIsForbidden($response)
    {
        $response->assertForbidden();
    }

    protected function assertResponseIsNotFound($response)
    {
        $response->assertNotFound();
    }

    protected function assertResponseIsMethodNotAllowed($response)
    {
        $response->assertMethodNotAllowed();
    }

    protected function assertResponseIsGone($response)
    {
        $response->assertGone();
    }

    protected function assertResponseIsTooManyRequests($response)
    {
        $response->assertTooManyRequests();
    }

    protected function assertResponseIsUnprocessableEntity($response)
    {
        $response->assertUnprocessableEntity();
    }

    protected function assertResponseIsLocked($response)
    {
        $response->assertLocked();
    }

    protected function assertResponseIsFailedDependency($response)
    {
        $response->assertFailedDependency();
    }

    protected function assertResponseIsPreconditionRequired($response)
    {
        $response->assertPreconditionRequired();
    }

    protected function assertResponseIsRequestEntityTooLarge($response)
    {
        $response->assertRequestEntityTooLarge();
    }

    protected function assertResponseIsUnsupportedMediaType($response)
    {
        $response->assertUnsupportedMediaType();
    }

    protected function assertResponseIsUnavailableForLegalReasons($response)
    {
        $response->assertUnavailableForLegalReasons();
    }

    protected function assertResponseIsInternalServerError($response)
    {
        $response->assertInternalServerError();
    }

    protected function assertResponseIsNotImplemented($response)
    {
        $response->assertNotImplemented();
    }

    protected function assertResponseIsBadGateway($response)
    {
        $response->assertBadGateway();
    }

    protected function assertResponseIsServiceUnavailable($response)
    {
        $response->assertServiceUnavailable();
    }

    protected function assertResponseIsGatewayTimeout($response)
    {
        $response->assertGatewayTimeout();
    }

    protected function assertResponseIsHttpVersionNotSupported($response)
    {
        $response->assertHttpVersionNotSupported();
    }
}
