# MCC News Aggregator - Comprehensive Testing Guide

## Overview

This document provides a comprehensive guide for testing the MCC News Aggregator Laravel application. The testing suite covers all major functionality including authentication, content management, user interactions, API endpoints, security, and integration workflows.

## Test Structure

### Test Categories

1. **Authentication Tests** (`tests/Feature/AuthenticationTest.php`)
   - User registration and login flows
   - MS365 and Gmail authentication
   - Admin authentication (SuperAdmin, Department Admin, Office Admin)
   - Password reset functionality
   - Session management
   - Logout functionality

2. **Content Management Tests** (`tests/Feature/ContentManagementTest.php`)
   - Announcement creation, editing, and deletion
   - Event management
   - News article management
   - Content publishing and unpublishing
   - Department-specific content visibility
   - Media file handling

3. **User Dashboard Tests** (`tests/Feature/UserDashboardTest.php`)
   - Dashboard access and content display
   - Content filtering by department
   - Event date formatting
   - TBD event handling
   - Comment system integration
   - Profile information display

4. **API Tests** (`tests/Feature/ApiTest.php`)
   - Chatbot API functionality
   - Comment API endpoints
   - Notification API endpoints
   - Content API endpoints
   - CORS handling
   - Rate limiting
   - Input validation

5. **Security Tests** (`tests/Feature/SecurityTest.php`)
   - SQL injection prevention
   - XSS protection
   - CSRF protection
   - Input validation and sanitization
   - Password security
   - File upload security
   - Authorization checks
   - Session security

6. **Integration Tests** (`tests/Feature/IntegrationTest.php`)
   - Complete user registration and login flow
   - Content creation and consumption workflow
   - Admin management workflow
   - Notification system workflow
   - Password reset workflow
   - Comment interaction workflow
   - Chatbot interaction workflow
   - Department visibility workflow
   - Content lifecycle workflow

## Running Tests

### Prerequisites

1. Ensure PHP 8.2+ is installed
2. Install Composer dependencies: `composer install`
3. Set up test database (SQLite in-memory)
4. Run migrations: `php artisan migrate`

### Running All Tests

```bash
# Run all tests
php run_tests.php all

# Or using PHPUnit directly
vendor/bin/phpunit

# Run with coverage report
php run_tests.php coverage
```

### Running Specific Test Suites

```bash
# Authentication tests
vendor/bin/phpunit tests/Feature/AuthenticationTest.php

# Content management tests
vendor/bin/phpunit tests/Feature/ContentManagementTest.php

# User dashboard tests
vendor/bin/phpunit tests/Feature/UserDashboardTest.php

# API tests
vendor/bin/phpunit tests/Feature/ApiTest.php

# Security tests
vendor/bin/phpunit tests/Feature/SecurityTest.php

# Integration tests
vendor/bin/phpunit tests/Feature/IntegrationTest.php
```

### Running Individual Tests

```bash
# Run specific test method
vendor/bin/phpunit --filter test_user_can_login_with_ms365_credentials

# Run tests with verbose output
vendor/bin/phpunit --verbose

# Run tests with colors
vendor/bin/phpunit --colors=always
```

## Test Configuration

### Environment Setup

The test environment is configured in `tests/TestConfiguration.php`:

- Uses SQLite in-memory database
- Clears all caches before running
- Seeds test data
- Provides helper methods for creating test entities

### Test Data

Test data includes:
- Test departments (BSIT, BSBA, BEED, BSED, BSHM)
- Test user roles (student, faculty)
- Test admin roles (super_admin, department_admin, office_admin)
- Test year levels (1st Year, 2nd Year, 3rd Year, 4th Year)

## Test Coverage Areas

### Authentication & Authorization
- ✅ User registration (MS365, Gmail)
- ✅ User login (MS365, Gmail, Admin)
- ✅ Password reset
- ✅ Session management
- ✅ Role-based access control
- ✅ Department-based access control

### Content Management
- ✅ Announcement CRUD operations
- ✅ Event CRUD operations
- ✅ News CRUD operations
- ✅ Content publishing/unpublishing
- ✅ Media file handling
- ✅ Department visibility

### User Interactions
- ✅ Dashboard content display
- ✅ Content filtering
- ✅ Comment system
- ✅ Notification system
- ✅ Profile management

### API Endpoints
- ✅ Chatbot API
- ✅ Comment API
- ✅ Notification API
- ✅ Content API
- ✅ Error handling
- ✅ Rate limiting

### Security
- ✅ Input validation
- ✅ XSS prevention
- ✅ SQL injection prevention
- ✅ CSRF protection
- ✅ File upload security
- ✅ Password security

### Integration Workflows
- ✅ Complete user registration flow
- ✅ Content creation and consumption
- ✅ Admin management workflows
- ✅ Notification workflows
- ✅ Comment interaction workflows

## Test Assertions

### Common Assertions

```php
// Status code assertions
$response->assertStatus(200);
$response->assertRedirect();
$response->assertOk();

// Content assertions
$response->assertSee('Expected Content');
$response->assertDontSee('Unwanted Content');

// Database assertions
$this->assertDatabaseHas('table', ['column' => 'value']);
$this->assertDatabaseMissing('table', ['column' => 'value']);

// Authentication assertions
$this->assertAuthenticated();
$this->assertGuest();

// JSON assertions
$response->assertJson(['key' => 'value']);
$response->assertJsonStructure(['key']);

// Session assertions
$response->assertSessionHas('key', 'value');
$response->assertSessionHasErrors(['field']);
```

### Custom Assertions

The `TestConfiguration` class provides custom assertion methods:

```php
// User access assertions
$this->assertUserCanAccess($user, $route);
$this->assertUserCannotAccess($user, $route);

// Content visibility assertions
$this->assertContentIsVisibleToUser($user, $content);
$this->assertContentIsVisibleToDepartment($department, $content);

// API response assertions
$this->assertApiResponse($response, 200, ['data']);
```

## Test Data Management

### Factory Usage

```php
// Create test user
$user = User::factory()->create([
    'role' => 'student',
    'department' => 'Bachelor of Science in Information Technology'
]);

// Create test admin
$admin = Admin::factory()->create([
    'role' => 'department_admin',
    'department' => 'Bachelor of Science in Information Technology'
]);

// Create test content
$announcement = Announcement::factory()->create([
    'title' => 'Test Announcement',
    'is_published' => true
]);
```

### Helper Methods

```php
// Using TestConfiguration helpers
$user = $this->createTestUser(['role' => 'faculty']);
$admin = $this->createTestAdmin(['role' => 'super_admin']);
$announcement = $this->createTestAnnouncement(['title' => 'Custom Title']);
```

## Continuous Integration

### GitHub Actions Example

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'
        
    - name: Install dependencies
      run: composer install --no-dev --optimize-autoloader
      
    - name: Run tests
      run: vendor/bin/phpunit --coverage-text
```

## Performance Testing

### Load Testing

```bash
# Run tests with performance monitoring
vendor/bin/phpunit --log-junit results.xml

# Generate coverage report
vendor/bin/phpunit --coverage-html coverage
```

### Memory Usage

```bash
# Monitor memory usage during tests
php -d memory_limit=512M vendor/bin/phpunit
```

## Debugging Tests

### Common Issues

1. **Database Connection Issues**
   - Ensure SQLite is available
   - Check database configuration
   - Verify migrations are running

2. **Authentication Issues**
   - Check user factory configuration
   - Verify password hashing
   - Ensure session configuration

3. **File Upload Issues**
   - Check file permissions
   - Verify storage configuration
   - Ensure test files exist

### Debugging Tips

```php
// Dump response content
$response->dump();

// Dump session data
$response->dumpSession();

// Dump database state
$this->dumpDatabase();

// Add debug output
dd($response->getContent());
```

## Best Practices

### Test Organization
- Group related tests in the same class
- Use descriptive test method names
- Keep tests focused on single functionality
- Use setUp() for common test data

### Test Data
- Use factories for consistent test data
- Clean up test data after each test
- Use realistic test data
- Avoid hardcoded values

### Assertions
- Use specific assertions over generic ones
- Test both positive and negative cases
- Verify database state changes
- Check response content and structure

### Performance
- Use RefreshDatabase trait for database tests
- Avoid unnecessary API calls
- Use mocks for external services
- Keep tests fast and focused

## Maintenance

### Regular Tasks
- Update test data when models change
- Add tests for new features
- Review and update test coverage
- Monitor test performance

### Test Updates
- Update tests when requirements change
- Refactor tests for better maintainability
- Remove obsolete tests
- Add edge case tests

## Conclusion

This comprehensive testing suite ensures the MCC News Aggregator application is robust, secure, and reliable. Regular testing helps maintain code quality and prevents regressions as the application evolves.

For questions or issues with the testing suite, please refer to the Laravel testing documentation or contact the development team.
