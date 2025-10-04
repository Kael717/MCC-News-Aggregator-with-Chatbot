<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Event;
use App\Models\News;
use App\Services\NotificationService;

class UserDashboardController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $user = auth()->user();
        $userDepartment = $user->department;

        // Get published announcements visible to this user
        $announcements = Announcement::where('is_published', true)
            ->visibleToUser($user)
            ->with('admin')
            ->latest()
            ->get();

        // Get published events visible to this user
        // Show both upcoming and recent past events for better user experience
        $events = Event::where('is_published', true)
            ->visibleToUser($user)
            ->with('admin')
            ->where(function($query) {
                // Show upcoming events and events from the last 90 days
                $query->where('event_date', '>=', now()->subDays(90))
                      ->orWhereNull('event_date'); // Include TBD events
            })
            ->orderByRaw('CASE WHEN event_date IS NULL THEN 1 ELSE 0 END') // TBD events last
            ->orderBy('event_date', 'asc')
            ->get();

        // Get published news visible to this user
        $news = News::where('is_published', true)
            ->visibleToUser($user)
            ->with('admin')
            ->latest()
            ->get();

        // Get statistics for the hero section (content visible to this user)
        $totalAnnouncements = Announcement::where('is_published', true)->visibleToUser($user)->count();
        $totalEvents = Event::where('is_published', true)->visibleToUser($user)->count();
        $totalNews = News::where('is_published', true)->visibleToUser($user)->count();

        // Get notification data for the current user
        $unreadNotificationsCount = $this->notificationService->getUnreadCount($user);
        $recentNotifications = $this->notificationService->getRecentNotifications($user, 5);

        return view('user.dashboard', compact(
            'announcements',
            'events',
            'news',
            'totalAnnouncements',
            'totalEvents',
            'totalNews',
            'unreadNotificationsCount',
            'recentNotifications'
        ));
    }

    /**
     * Get a specific announcement for notification click.
     */
    public function getAnnouncement($id)
    {
        $user = auth()->user();
        $announcement = Announcement::where('id', $id)
            ->where('is_published', true)
            ->visibleToUser($user)
            ->with('admin')
            ->first();

        if (!$announcement) {
            return response()->json([
                'success' => false,
                'error' => 'Announcement not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'content' => $announcement
        ]);
    }

    /**
     * Get a specific event for notification click.
     */
    public function getEvent($id)
    {
        $user = auth()->user();
        $event = Event::where('id', $id)
            ->where('is_published', true)
            ->visibleToUser($user)
            ->with('admin')
            ->first();

        if (!$event) {
            return response()->json([
                'success' => false,
                'error' => 'Event not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'content' => $event
        ]);
    }

    /**
     * Get a specific news article for notification click.
     */
    public function getNews($id)
    {
        $user = auth()->user();
        $news = News::where('id', $id)
            ->where('is_published', true)
            ->visibleToUser($user)
            ->with('admin')
            ->first();

        if (!$news) {
            return response()->json([
                'success' => false,
                'error' => 'News article not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'content' => $news
        ]);
    }
}
