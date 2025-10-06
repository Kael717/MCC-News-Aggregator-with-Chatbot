<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminFacultyController;
use App\Http\Controllers\AdminStudentController;
use App\Http\Controllers\SuperAdminDashboardController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\DepartmentAdminDashboardController;
use App\Http\Controllers\OfficeAdminController;
use App\Http\Controllers\OfficeAdminDashboardController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\DepartmentAdminAuthController;
use App\Http\Controllers\OfficeAdminAuthController;
use App\Http\Controllers\SuperAdminAuthController;
use App\Http\Controllers\PublicContentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UnifiedAuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Auth\GmailAuthController;
use App\Http\Controllers\Auth\MS365AuthController;
use App\Http\Controllers\Auth\MS365OAuthController;
use Illuminate\Support\Facades\Artisan;

// TEMPORARY STORAGE FIX ROUTES - DELETE AFTER RUNNING ONCE
Route::get('/fix-storage', function () {
    try {
        $target = public_path('storage');
        $source = storage_path('app/public');
        
        // Check if target already exists
        if (file_exists($target)) {
            return response()->json([
                'success' => true,
                'message' => 'Storage directory already exists',
                'target' => $target,
                'source' => $source,
                'note' => 'Storage is already configured'
            ]);
        }
        
        // Create storage directory in public folder
        if (!file_exists($target)) {
            if (mkdir($target, 0755, true)) {
                // Create .gitignore to prevent accidental commits
                file_put_contents($target . '/.gitignore', "*\n!.gitignore\n");
                
                // Create a simple index.php to prevent directory listing
                file_put_contents($target . '/index.php', "<?php\n// Storage directory\n");
                
                return response()->json([
                    'success' => true,
                    'message' => 'Storage directory created successfully!',
                    'target' => $target,
                    'source' => $source,
                    'note' => 'Files will be served via .htaccess rewrite rules'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create storage directory. Check permissions.'
                ], 500);
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Storage directory already exists',
            'target' => $target
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error creating storage directory: ' . $e->getMessage()
        ], 500);
    }
})->name('fix.storage');

Route::get('/fix-cache', function () {
    try {
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        
        return response()->json([
            'success' => true,
            'message' => 'All caches cleared successfully!',
            'commands' => [
                'config:clear' => 'Configuration cache cleared',
                'cache:clear' => 'Application cache cleared',
                'route:clear' => 'Route cache cleared',
                'view:clear' => 'View cache cleared'
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error clearing caches: ' . $e->getMessage()
        ], 500);
    }
})->name('fix.cache');

Route::get('/fix-migration', function () {
    try {
        // Run the specific login_attempts migration
        Artisan::call('migrate', [
            '--path' => 'database/migrations/2025_10_05_170646_create_login_attempts_table.php',
            '--force' => true
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Login attempts migration run successfully!',
            'output' => Artisan::output()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error running migration: ' . $e->getMessage()
        ], 500);
    }
})->name('fix.migration');

// Show welcome page at root
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Unified login routes (default login)

Route::get('/login', [UnifiedAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UnifiedAuthController::class, 'login'])->name('unified.login');
Route::post('/check-lockout-status', [UnifiedAuthController::class, 'checkLockoutStatus'])->name('check.lockout.status');

// Gmail Authentication Routes
Route::get('/signup', [UnifiedAuthController::class, 'showSignupForm'])->name('gmail.signup');
Route::post('/signup', [UnifiedAuthController::class, 'sendRegistrationLink'])->name('gmail.signup.send');
Route::get('/register', [UnifiedAuthController::class, 'showRegistrationForm'])->name('gmail.register.form');
Route::post('/register', [UnifiedAuthController::class, 'completeRegistration'])->name('gmail.register.complete');

// MS365 Authentication Routes
Route::get('/ms365/signup', [MS365OAuthController::class, 'showSignupForm'])->name('ms365.signup');
Route::post('/ms365/signup', [MS365OAuthController::class, 'sendSignupLink'])->name('ms365.signup.send');
Route::get('/ms365/register', [MS365OAuthController::class, 'showRegisterForm'])->name('ms365.register.form');
Route::post('/ms365/register', [MS365OAuthController::class, 'handleRegister'])->name('ms365.register.complete');

// MS365 OAuth2 Routes
Route::get('/auth/ms365/redirect', [MS365OAuthController::class, 'redirectToProvider'])->name('ms365.oauth.redirect');
Route::get('/auth/ms365/callback', [MS365OAuthController::class, 'handleProviderCallback'])->name('ms365.oauth.callback');
Route::get('/register/{token}', [MS365OAuthController::class, 'showRegisterForm'])->name('ms365.register.form.token');
Route::post('/register', [MS365OAuthController::class, 'handleRegister'])->name('ms365.register.complete.token');

Route::post('/logout', [UnifiedAuthController::class, 'logout'])->name('logout');


// Test route for image debugging (remove in production)
Route::get('/test-images', function () {
    return view('test-images');
})->name('test.images');

// Public content routes (no authentication required)
Route::get('/announcements', [PublicContentController::class, 'announcements'])->name('public.announcements.index');
Route::get('/announcements/{announcement}', [PublicContentController::class, 'showAnnouncement'])->name('public.announcements.show');
Route::get('/events', [PublicContentController::class, 'events'])->name('public.events.index');
Route::get('/events/{event}', [PublicContentController::class, 'showEvent'])->name('public.events.show');
Route::get('/news', [PublicContentController::class, 'news'])->name('public.news.index');
Route::get('/news/{news}', [PublicContentController::class, 'showNews'])->name('public.news.show');






// Admin Routes
Route::prefix('admin')->group(function () {
    // Auth routes
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('login', [AdminAuthController::class, 'login']);
    Route::get('register', [AdminAuthController::class, 'showRegisterForm'])->name('admin.register');
    Route::post('register', [AdminAuthController::class, 'register']);
    
    // Protected routes - Only department admins can access these
    Route::middleware(['auth:admin', \App\Http\Middleware\DepartmentAdminAuth::class])->group(function () {
        Route::get('dashboard', [DepartmentAdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
        
        // Content management routes (to be implemented)
        // Announcements CRUD
        Route::resource('announcements', AnnouncementController::class);

        // Events CRUD  
        Route::resource('events', EventController::class);

        // News CRUD
        Route::resource('news', NewsController::class);
        
        // Faculty management routes
        Route::get('faculty', [AdminFacultyController::class, 'index'])->name('admin.faculty.index');
        Route::get('faculty/{faculty}/edit', [AdminFacultyController::class, 'edit'])->name('admin.faculty.edit');
        Route::put('faculty/{faculty}', [AdminFacultyController::class, 'update'])->name('admin.faculty.update');
        Route::delete('faculty/{faculty}', [AdminFacultyController::class, 'destroy'])->name('admin.faculty.delete');
        
        // Student management routes
        Route::get('students', [AdminStudentController::class, 'index'])->name('admin.students');
        Route::get('students/{student}/edit', [AdminStudentController::class, 'edit'])->name('admin.students.edit');
        Route::put('students/{student}', [AdminStudentController::class, 'update'])->name('admin.students.update');
        Route::delete('students/{student}', [AdminStudentController::class, 'destroy'])->name('admin.students.delete');
    });
});

// SuperAdmin Routes
Route::prefix('superadmin')->group(function () {
    // Auth routes (dedicated super admin auth)
    Route::get('login', [SuperAdminAuthController::class, 'showLoginForm'])->name('superadmin.login');
    Route::post('login', [SuperAdminAuthController::class, 'login']);
    Route::post('logout', [SuperAdminAuthController::class, 'logout'])->name('superadmin.logout');

    // Protected SuperAdmin routes
    Route::middleware(['auth:admin', \App\Http\Middleware\SuperAdminAuth::class])->group(function () {
        Route::get('dashboard', [SuperAdminDashboardController::class, 'index'])->name('superadmin.dashboard');

        // Admin management routes
        Route::resource('admins', SuperAdminController::class, [
            'as' => 'superadmin'
        ]);

        // Department Admin management routes
        Route::get('department-admins/create', [SuperAdminController::class, 'createDepartmentAdmin'])->name('superadmin.department-admins.create');
        Route::post('department-admins', [SuperAdminController::class, 'storeDepartmentAdmin'])->name('superadmin.department-admins.store');
        Route::get('department-admins', [SuperAdminController::class, 'departmentAdmins'])->name('superadmin.department-admins.index');

        // Office Admin management routes
        Route::resource('office-admins', OfficeAdminController::class, [
            'as' => 'superadmin'
        ]);

        // Content management routes (inherited from admin)
        Route::resource('announcements', AnnouncementController::class, [
            'as' => 'superadmin'
        ]);
        
        // Modal routes for announcements
        Route::get('announcements/{announcement}/modal-show', [AnnouncementController::class, 'showModal'])->name('superadmin.announcements.modal-show');
        Route::get('announcements/{announcement}/modal-edit', [AnnouncementController::class, 'editModal'])->name('superadmin.announcements.modal-edit');
        
        Route::resource('events', EventController::class, [
            'as' => 'superadmin'
        ]);
        Route::resource('news', NewsController::class, [
            'as' => 'superadmin'
        ]);
        Route::get('news/{news}/show-data', [NewsController::class, 'showData'])->name('superadmin.news.show-data');

        // Faculty management routes
        Route::get('faculty', [AdminFacultyController::class, 'index'])->name('superadmin.faculty.index');
        Route::get('faculty/create', [AdminFacultyController::class, 'create'])->name('superadmin.faculty.create');
        Route::post('faculty', [AdminFacultyController::class, 'store'])->name('superadmin.faculty.store');
        Route::get('faculty/{faculty}', [AdminFacultyController::class, 'show'])->name('superadmin.faculty.show');
        Route::get('faculty/{faculty}/edit', [AdminFacultyController::class, 'edit'])->name('superadmin.faculty.edit');
        Route::put('faculty/{faculty}', [AdminFacultyController::class, 'update'])->name('superadmin.faculty.update');
        Route::delete('faculty/{faculty}', [AdminFacultyController::class, 'destroy'])->name('superadmin.faculty.destroy');

        // Student management routes
        Route::get('students', [AdminStudentController::class, 'index'])->name('superadmin.students.index');
        Route::get('students/create', [AdminStudentController::class, 'create'])->name('superadmin.students.create');
        Route::post('students', [AdminStudentController::class, 'store'])->name('superadmin.students.store');
        Route::get('students/{student}', [AdminStudentController::class, 'show'])->name('superadmin.students.show');
        Route::get('students/{student}/edit', [AdminStudentController::class, 'edit'])->name('superadmin.students.edit');
        Route::put('students/{student}', [AdminStudentController::class, 'update'])->name('superadmin.students.update');
        Route::delete('students/{student}', [AdminStudentController::class, 'destroy'])->name('superadmin.students.destroy');
    });
});

// Department Admin Routes
Route::prefix('department-admin')->group(function () {
    // Auth routes (dedicated department admin auth)
    Route::get('login', [DepartmentAdminAuthController::class, 'showLoginForm'])->name('department-admin.login');
    Route::post('login', [DepartmentAdminAuthController::class, 'login']);
    Route::post('logout', [DepartmentAdminAuthController::class, 'logout'])->name('department-admin.logout');

    // Protected Department Admin routes
    Route::middleware(['auth:admin', \App\Http\Middleware\DepartmentAdminAuth::class])->group(function () {
        Route::get('dashboard', [DepartmentAdminDashboardController::class, 'index'])->name('department-admin.dashboard');

        // Content management routes (limited to department admin's content)
        Route::resource('announcements', AnnouncementController::class, [
            'as' => 'department-admin'
        ]);
        Route::resource('events', EventController::class, [
            'as' => 'department-admin'
        ]);
        Route::resource('news', NewsController::class, [
            'as' => 'department-admin'
        ]);
    });
});

// Office Admin Routes
Route::prefix('office-admin')->name('office-admin.')->group(function () {
    Route::get('login', [OfficeAdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [OfficeAdminAuthController::class, 'login']);

    // Protected Office Admin routes
    Route::middleware(['auth:admin', \App\Http\Middleware\OfficeAdminAuth::class])->group(function () {
        Route::get('dashboard', [OfficeAdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('logout', [OfficeAdminAuthController::class, 'logout'])->name('logout');

        // Content management routes (limited to office admin's content)
        Route::resource('announcements', AnnouncementController::class);
        Route::resource('events', EventController::class);
        Route::resource('news', NewsController::class);
    });
});

// User Routes
Route::prefix('user')->group(function () {
    // Auth routes
    Route::get('login', [UserAuthController::class, 'showLoginForm'])->name('user.login');
    Route::post('login', [UserAuthController::class, 'login']);
    Route::get('register', [UserAuthController::class, 'showRegisterForm'])->name('user.register');
    Route::post('register', [UserAuthController::class, 'register']);
    
    // Protected routes
    Route::middleware('auth')->group(function () {
        Route::get('dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
        Route::post('logout', [UserAuthController::class, 'logout'])->name('user.logout');

        // Notification routes
        Route::get('notifications', [NotificationController::class, 'index'])->name('user.notifications.index');
        Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('user.notifications.read');
        Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('user.notifications.mark-all-read');
        Route::get('notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('user.notifications.unread-count');
        Route::delete('notifications/{id}', [NotificationController::class, 'destroy'])->name('user.notifications.destroy');

        // Content routes for notifications
        Route::get('content/announcement/{id}', [UserDashboardController::class, 'getAnnouncement'])->name('user.content.announcement');
        Route::get('content/event/{id}', [UserDashboardController::class, 'getEvent'])->name('user.content.event');
        Route::get('content/news/{id}', [UserDashboardController::class, 'getNews'])->name('user.content.news');

        // Comment routes
        Route::get('content/{type}/{id}/comments', [CommentController::class, 'getComments'])->name('comments.get');
        Route::post('comments', [CommentController::class, 'store'])->name('comments.store');
        Route::put('comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
        Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

        // Profile routes
        Route::post('profile/update', [\App\Http\Controllers\UserProfileController::class, 'updateProfile'])->name('user.profile.update');
        Route::post('profile/upload-picture', [\App\Http\Controllers\UserProfileController::class, 'uploadProfilePicture'])->name('user.profile.upload-picture');
        Route::delete('profile/remove-picture', [\App\Http\Controllers\UserProfileController::class, 'removeProfilePicture'])->name('user.profile.remove-picture');

        // Test route for comment functionality
        Route::get('comments/test', function() {
            $user = auth()->user();
            $announcement = \App\Models\Announcement::find(60);

            return response()->json([
                'success' => true,
                'message' => 'Comment routes are working',
                'user' => $user ? $user->name : 'Not authenticated',
                'user_id' => $user ? $user->id : null,
                'announcement_exists' => $announcement ? true : false,
                'announcement_published' => $announcement ? $announcement->is_published : null,
                'csrf_token' => csrf_token(),
                'can_comment' => $user && $announcement && $announcement->is_published && $announcement->isVisibleToUser($user)
            ]);
        });

        // Debug route to check comment isolation
        Route::get('comments/debug/{type}/{id}', function($type, $id) {
            $user = auth()->user();
            $commentableModel = null;
            
            switch ($type) {
                case 'announcement':
                    $commentableModel = \App\Models\Announcement::find($id);
                    break;
                case 'event':
                    $commentableModel = \App\Models\Event::find($id);
                    break;
                case 'news':
                    $commentableModel = \App\Models\News::find($id);
                    break;
            }

            if (!$commentableModel) {
                return response()->json(['error' => 'Content not found'], 404);
            }

            $comments = \App\Models\Comment::where('commentable_type', get_class($commentableModel))
                ->where('commentable_id', $commentableModel->id)
                ->get();

            return response()->json([
                'content_type' => $type,
                'content_id' => $id,
                'model_class' => get_class($commentableModel),
                'model_id' => $commentableModel->id,
                'comments_count' => $comments->count(),
                'comments' => $comments->map(function($comment) {
                    return [
                        'id' => $comment->id,
                        'content' => $comment->content,
                        'user_id' => $comment->user_id,
                        'commentable_type' => $comment->commentable_type,
                        'commentable_id' => $comment->commentable_id,
                        'parent_id' => $comment->parent_id,
                        'created_at' => $comment->created_at
                    ];
                })
            ]);
        });
    });
    // Test route for DeepSeek API
Route::get('/test-deepseek', function () {
    // Check if API key exists in environment
    $apiKey = env('DEEPSEEK_API_KEY');
    
    // Debug information
    $debugInfo = [
        'api_key_exists' => !empty($apiKey),
        'api_key_length' => $apiKey ? strlen($apiKey) : 0,
        'api_key_prefix' => $apiKey ? substr($apiKey, 0, 10) . '...' : 'Not found',
        'env_file_exists' => file_exists(base_path('.env')),
        'config_cached' => file_exists(base_path('bootstrap/cache/config.php'))
    ];
    if (!$apiKey) {
        return response()->json([
            'error' => 'API key not configured',
            'debug' => $debugInfo,
            'instructions' => [
                '1. Make sure DEEPSEEK_API_KEY is in your .env file',
                '2. Run: php artisan config:clear',
                '3. Run: php artisan cache:clear',
                '4. Restart your server'
            ]
        ]);
         }
    
    try {
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post('https://api.deepseek.com/v1/chat/completions', [
            'model' => 'deepseek-chat',
            'messages' => [
                ['role' => 'user', 'content' => 'Hello, can you respond with "API connection successful"?']
            ],
            'max_tokens' => 50,
            'temperature' => 0.7
        ]);
        
        return response()->json([
            'status' => $response->status(),
            'success' => $response->successful(),
            'debug' => $debugInfo,
            'response' => $response->json(),
            'raw_response' => $response->body()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'debug' => $debugInfo,
            'trace' => $e->getTraceAsString()
        ]);
    }
})->name('test.deepseek');
// Chatbot API route (accessible from welcome page and dashboard)
Route::post('/api/chatbot', [ChatbotController::class, 'chat'])->name('api.chatbot');
Route::put('/user/update-settings', [UserAuthController::class, 'updateSettings'])->name('user.update-settings');

});

// Test route for department visibility
Route::get('/test-department-counts', function () {
    $user = auth()->user();
    if (!$user) {
        return response()->json(['error' => 'Not authenticated']);
    }

    $userDepartment = $user->department;

    $totalAnnouncements = App\Models\Announcement::where('is_published', true)
        ->visibleToDepartment($userDepartment)
        ->count();
    $totalEvents = App\Models\Event::where('is_published', true)
        ->visibleToDepartment($userDepartment)
        ->count();
    $totalNews = App\Models\News::where('is_published', true)
        ->visibleToDepartment($userDepartment)
        ->count();

    $allAnnouncements = App\Models\Announcement::where('is_published', true)->count();
    $allEvents = App\Models\Event::where('is_published', true)->count();
    $allNews = App\Models\News::where('is_published', true)->count();

    return response()->json([
        'user_department' => $userDepartment,
        'visible_to_user' => [
            'announcements' => $totalAnnouncements,
            'events' => $totalEvents,
            'news' => $totalNews
        ],
        'total_published' => [
            'announcements' => $allAnnouncements,
            'events' => $allEvents,
            'news' => $allNews
        ]
    ]);
})->middleware('auth');

// Test route for all departments visibility
Route::get('/test-all-departments', function () {
    $departments = ['BSIT', 'BSBA', 'BEED', 'BSHM', 'BSED'];
    $results = [];

    foreach ($departments as $dept) {
        $results[$dept] = [
            'announcements' => App\Models\Announcement::where('is_published', true)
                ->visibleToDepartment($dept)
                ->count(),
            'events' => App\Models\Event::where('is_published', true)
                ->visibleToDepartment($dept)
                ->count(),
            'news' => App\Models\News::where('is_published', true)
                ->visibleToDepartment($dept)
                ->count(),
        ];
    }

    $totalCounts = [
        'announcements' => App\Models\Announcement::where('is_published', true)->count(),
        'events' => App\Models\Event::where('is_published', true)->count(),
        'news' => App\Models\News::where('is_published', true)->count(),
    ];

    return response()->json([
        'department_visibility' => $results,
        'total_published' => $totalCounts,
        'admins' => App\Models\Admin::where('role', 'department_admin')->get(['username', 'department'])
    ]);
});

// Test route for student profiles
Route::get('/test-student-profiles', function () {
    $students = App\Models\User::where('role', 'student')
        ->whereNotNull('department')
        ->get(['first_name', 'surname', 'department', 'year_level', 'ms365_account']);

    return response()->json([
        'students' => $students,
        'total_students' => $students->count(),
        'departments' => $students->groupBy('department')->map(function($group) {
            return $group->count();
        })
    ]);
});

// Test route for MS365 OAuth2 system
Route::get('/test-ms365-oauth', function () {
    $ms365Accounts = App\Models\Ms365Account::all(['display_name', 'user_principal_name', 'first_name', 'last_name']);
    
    return response()->json([
        'ms365_accounts' => $ms365Accounts,
        'total_accounts' => $ms365Accounts->count(),
        'system_status' => [
            'socialite_installed' => class_exists('Laravel\Socialite\Facades\Socialite'),
            'microsoft_provider_installed' => class_exists('SocialiteProviders\Microsoft\MicrosoftExtendSocialite'),
            'ms365_oauth_controller_exists' => class_exists('App\Http\Controllers\Auth\MS365OAuthController'),
            'ms365_account_model_exists' => class_exists('App\Models\Ms365Account'),
            'microsoft_graph_service_exists' => class_exists('App\Services\MicrosoftGraphService'),
        ]
    ]);
});

// Password Reset Routes
Route::get('/forgot-password', [UnifiedAuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [UnifiedAuthController::class, 'sendPasswordResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [UnifiedAuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [UnifiedAuthController::class, 'resetPassword'])->name('password.update');

// Laravel's default auth routes (excluding login since we have custom unified login)
Auth::routes(['login' => false, 'reset' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');