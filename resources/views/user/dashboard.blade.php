<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - MCC News Aggregator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .bulletin-board {
            background: #e9e2d0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border-radius: 12px;
            position: relative;
        }
        
        .bulletin-board::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 40px;
            background: #8b5a2b;
            border-radius: 12px 12px 0 0;
        }
        
        .section {
            background: #fff;
            border: 1px solid #d4c9a9;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            margin-bottom: 20px;
        }
        
        .section:hover {
            transform: translateY(-5px);
        }
        
        .pin {
            position: relative;
        }
        
        .pin::before {
            content: '';
            position: absolute;
            top: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 16px;
            height: 16px;
            background: radial-gradient(circle, #ffd700 30%, #daa520 90%);
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        /* Modal transitions now handled by Alpine.js */
        
        .item-hover {
            transition: all 0.2s ease;
        }
        
        .item-hover:hover {
            background-color: #f8f9fa;
            padding-left: 8px;
            border-left: 3px solid;
        }
        
        .announcement-hover:hover {
            border-left-color: #3b82f6;
        }
        
        .event-hover:hover {
            border-left-color: #10b981;
        }
        
        .news-hover:hover {
            border-left-color: #ef4444;
        }
        
        .profile-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .profile-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .announcement-item {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 12px;
            border: 1px solid #e5e7eb;
            transition: all 0.2s ease;
        }
        
        .announcement-item:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        
        /* Enhanced modal styles - now using Tailwind classes */
        
        .modal-category {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .category-announcement {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .category-event {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .category-news {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        
        }

        .modal-location-container {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 12px 16px;
            margin: 12px 0;
            box-shadow: 0 2px 4px rgba(34, 197, 94, 0.1);
        }

        .modal-location {
            color: #16a34a;
            font-size: 0.875rem;
            font-weight: 500;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modal-location::before {
            content: "\f3c5";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            font-size: 1rem;
            color: #16a34a;
            filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.1));
        }
        
        .modal-content {
            margin-top: 1rem;
            line-height: 1.6;
            color: #374151;
        }
        
        .close-button {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6b7280;
            transition: color 0.2s;
        }
        
        .close-button:hover {
            color: #374151;
        }
        
        /* Image and video styles */
        .item-media {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 6px;
            margin-bottom: 10px;
        }
        
        .video-container {
            position: relative;
            width: 100%;
            height: 160px;
            overflow: hidden;
            border-radius: 6px;
            margin-bottom: 10px;
        }
        
        .video-container video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .play-button {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.7);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        .play-button i {
            color: white;
            font-size: 20px;
        }
        
        .modal-media {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 1rem;
            max-height: 50vh;
            object-fit: contain;
            object-position: center;
            background-color: #f8f9fa;
        }
        
        .modal-video {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 1rem;
            max-height: 50vh;
            background-color: #000;
        }
        
        /* Enhanced Modal Styles with Media Grid Layout */
        .modal-container {
            max-width: 95vw;
            max-height: 95vh;
            width: 100%;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            transform: scale(0.7);
            transition: transform 0.4s ease;
        }

        .modal-container.active {
            transform: scale(1);
        }

        @media (min-width: 640px) {
            .modal-container {
                max-width: 90vw;
                max-width: 900px;
            }
        }

        @media (min-width: 1024px) {
            .modal-container {
                max-width: 85vw;
                max-width: 1000px;
            }
        }

        /* Media Container Grid Layout */
        .modal-media-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .modal-media-item {
            border-radius: 8px;
            overflow: hidden;
            height: 200px;
            background-color: #f8f9fa;
        }

        .modal-media-item img, 
        .modal-media-item video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .modal-video-container {
            grid-column: 1 / -1;
            height: 300px;
            background-color: #000;
            border-radius: 8px;
            overflow: hidden;
        }

        .modal-video-container video {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* Single media item layouts */
        .modal-single-image {
            width: 100%;
            height: auto;
            max-height: 70vh;
            border-radius: 8px;
            margin-bottom: 1rem;
            object-fit: contain;
            background-color: #f8f9fa;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .modal-single-video {
            width: 100%;
            height: auto;
            max-height: 50vh;
            border-radius: 8px;
            margin-bottom: 1rem;
            background-color: #000;
        }
        
        .modal-content-area {
            max-height: calc(95vh - 120px);
            overflow-y: auto;
        }
        
        @media (max-width: 768px) {
            .modal-content-area {
                max-height: calc(95vh - 100px);
                padding: 1rem;
            }
            
            .modal-media {
                max-height: 40vh;
            }
            
            .modal-video {
                max-height: 40vh;
            }
            
            /* Mobile modal header adjustments */
            .modal-header {
                padding: 1rem;
            }
            
            .modal-header h3 {
                font-size: 1.25rem;
                line-height: 1.4;
            }
            
            /* Better touch targets for mobile */
            .modal-close-btn {
                padding: 0.5rem;
                min-width: 44px;
                min-height: 44px;
            }
        }
        
        /* Extra small screens */
        @media (max-width: 480px) {
            .modal-container {
                max-width: 98vw;
                margin: 0.5rem;
            }
            
            .modal-content-area {
                padding: 0.75rem;
                max-height: calc(95vh - 80px);
            }
            
            .modal-media {
                max-height: 35vh;
            }
            
            .modal-video {
                max-height: 35vh;
            }
        }
        
        /* Row layout for sections */
        .row-layout {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .section-content {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
        }
        
        /* Mobile Responsive Styles for Modal Media */
        @media (max-width: 768px) {
            .bulletin-board::before {
                height: 30px;
            }
            
            .section-content {
                grid-template-columns: 1fr;
            }
            
            .modal-media-container {
                grid-template-columns: 1fr;
            }
            
            .modal-media-item {
                height: 150px;
            }
            
            .modal-video-container {
                height: 200px;
            }
        }

        /* Extra small screens */
        @media (max-width: 480px) {
            .modal-media-item {
                height: 120px;
            }
            
            .modal-video-container {
                height: 180px;
            }
        }
        
        /* SweetAlert Custom Styling */
        .swal-popup-custom {
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .swal-title-custom {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: #1f2937;
        }
        
        .swal-content-custom {
            font-family: 'Poppins', sans-serif;
            color: #6b7280;
        }
        
        .swal2-popup {
            font-family: 'Poppins', sans-serif;
        }
        
        .swal2-confirm {
            border-radius: 8px;
            font-weight: 600;
            padding: 10px 20px;
        }
        
        .swal2-cancel {
            border-radius: 8px;
            font-weight: 600;
            padding: 10px 20px;
        }
        
        /* Enhanced Profile Action Buttons */
        .profile-action-btn {
            position: relative;
            overflow: hidden;
        }
        
        .profile-action-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.3s ease, height 0.3s ease;
        }
        
        .profile-action-btn:hover::before {
            width: 100%;
            height: 100%;
        }
        
        /* Mobile responsive adjustments for profile buttons */
        @media (max-width: 768px) {
            .profile-action-btn {
                width: 52px !important;
                height: 52px !important;
                min-height: 52px;
                min-width: 52px;
                touch-action: manipulation;
                -webkit-tap-highlight-color: transparent;
            }
            
            .profile-action-btn i {
                font-size: 1.25rem !important;
            }
        }
        
        @media (max-width: 480px) {
            .profile-action-btn {
                width: 48px !important;
                height: 48px !important;
                min-height: 48px;
                min-width: 48px;
            }
            
            .profile-action-btn i {
                font-size: 1.125rem !important;
            }
        }
        
        @media (max-width: 360px) {
            .profile-action-btn {
                width: 44px !important;
                height: 44px !important;
                min-height: 44px;
                min-width: 44px;
            }
            
            .profile-action-btn i {
                font-size: 1rem !important;
            }
        }
        
        /* Tooltip enhancement */
        .profile-action-btn[title]:hover::after {
            content: attr(title);
            position: absolute;
            bottom: -35px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            white-space: nowrap;
            z-index: 1000;
            opacity: 0;
            animation: tooltipFadeIn 0.3s ease forwards;
        }
        
        @keyframes tooltipFadeIn {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(-5px);
            }
            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }
        
        /* Enhanced Profile Modal Buttons */
        .profile-modal-btn {
            position: relative;
            overflow: hidden;
            min-height: 48px;
            font-weight: 600;
            letter-spacing: 0.025em;
            border: none;
            cursor: pointer;
            user-select: none;
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
        }
        
        .profile-modal-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .profile-modal-btn:hover::before {
            left: 100%;
        }
        
        .profile-modal-btn:active {
            transform: scale(0.98);
        }
        
        /* Button-specific styles */
        .logout-btn:hover {
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
        }
        
        .edit-btn:hover {
            box-shadow: 0 8px 25px rgba(147, 51, 234, 0.4);
        }
        
        .close-btn:hover {
            box-shadow: 0 8px 25px rgba(107, 114, 128, 0.4);
        }
        
        /* Mobile responsive adjustments for profile modal buttons */
        @media (max-width: 640px) {
            .profile-modal-btn {
                min-height: 52px;
                font-size: 0.9375rem;
                padding: 0.875rem 1.5rem !important;
            }
            
            .profile-modal-btn i {
                font-size: 1.125rem;
            }
            
            .profile-modal-btn span {
                font-weight: 600;
            }
        }
        
        @media (max-width: 480px) {
            .profile-modal-btn {
                min-height: 48px;
                font-size: 0.875rem;
                padding: 0.75rem 1.25rem !important;
            }
            
            .profile-modal-btn i {
                font-size: 1rem;
            }
        }
        
        /* Enhanced modal footer */
        .modal-footer-gradient {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            border-top: 1px solid #e5e7eb;
        }
        
        /* Pulse animation for active states */
        @keyframes buttonPulse {
            0% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
            }
        }
        
        .profile-modal-btn:focus {
            outline: none;
            animation: buttonPulse 1.5s infinite;
        }
        
        /* Profile Edit Icon Styles */
        .profile-edit-icon {
            position: relative;
            overflow: hidden;
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
        }
        
        .profile-edit-icon::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(147, 51, 234, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.3s ease, height 0.3s ease;
        }
        
        .profile-edit-icon:hover::before {
            width: 100%;
            height: 100%;
        }
        
        .profile-edit-icon:active {
            transform: scale(0.95);
        }
        
        /* Mobile responsive adjustments for profile edit icon */
        @media (max-width: 768px) {
            .profile-edit-icon {
                width: 36px !important;
                height: 36px !important;
                min-height: 36px;
                min-width: 36px;
            }
            
            .profile-edit-icon i {
                font-size: 0.875rem !important;
            }
        }
        
        @media (max-width: 480px) {
            .profile-edit-icon {
                width: 32px !important;
                height: 32px !important;
                min-height: 32px;
                min-width: 32px;
            }
            
            .profile-edit-icon i {
                font-size: 0.75rem !important;
            }
        }
    </style>
</head>
<body class="py-8 px-4" x-data="dashboardData()">
    <div class="container mx-auto max-w-7xl">
        <header class="mb-8 text-center relative">
            <div class="flex justify-between items-center mb-4">
                <div></div>
                <div>
                    <h1 class="text-4xl font-bold text-gray-800 mb-2">MCC News Aggregator</h1>
                    <p class="text-gray-600">Your central hub for announcements, events, and news</p>
                </div>
                <div class="relative">
                    <button @click="toggleNotifications()" 
                            class="relative p-3 bg-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110">
                        <i class="fas fa-bell text-2xl text-gray-600 hover:text-blue-600 transition-colors"></i>
                        <span x-show="notificationCount > 0" 
                              x-text="notificationCount" 
                              class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center font-bold animate-pulse"></span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Notification Dropdown -->
        <div x-show="showNotifications" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="fixed top-20 right-4 z-50 w-80 bg-white rounded-lg shadow-xl border border-gray-200"
             @click.away="showNotifications = false">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Notifications</h3>
                    <button @click="showNotifications = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="max-h-64 overflow-y-auto">
                <template x-if="notifications.length === 0">
                    <div class="p-4 text-center text-gray-500">
                        <i class="fas fa-bell-slash text-2xl mb-2"></i>
                        <p>No new notifications</p>
                    </div>
                </template>
                <template x-for="notification in notifications" :key="notification.id">
                    <div class="p-4 border-b border-gray-100 hover:bg-gray-50" 
                         :class="{ 'bg-blue-50': !notification.is_read }">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center" 
                                 :class="notification.is_read ? 'bg-gray-100' : 'bg-blue-100'">
                                <i class="text-sm" 
                                   :class="notification.is_read ? 'fas fa-bell text-gray-600' : 'fas fa-bell text-blue-600'"></i>
                            </div>
                            <div class="flex-1 cursor-pointer" @click="handleNotificationClick(notification)">
                                <p class="text-sm font-medium" 
                                   :class="notification.is_read ? 'text-gray-700' : 'text-gray-900'"
                                   x-text="notification.title"></p>
                                <p class="text-xs text-gray-500" x-text="notification.message"></p>
                                <p class="text-xs text-gray-400 mt-1" x-text="notification.created_at"></p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div x-show="!notification.is_read" class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                <button @click="removeNotification(notification.id)" 
                                        class="text-xs text-gray-500 hover:text-red-600 transition-colors"
                                        title="Remove notification">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Profile Card at the Top -->
        <div class="profile-card bg-white rounded-xl shadow-md p-6 mb-8 flex items-center justify-between hover:shadow-lg transition-all duration-300 cursor-pointer" @click="profileModal = true">
            <div class="flex items-center">
                <div class="relative w-16 h-16 mr-6">
                    @if(auth()->user()->hasProfilePicture)
                        <img src="{{ auth()->user()->profilePictureUrl }}" 
                             alt="Profile Picture" 
                             class="w-16 h-16 rounded-full object-cover border-2 border-purple-200 shadow-sm">
                    @else
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-sm">
                            {{ auth()->user()->initials }}
                        </div>
                    @endif
                    <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 rounded-full border-2 border-white flex items-center justify-center">
                        <i class="fas fa-camera text-white text-xs"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-3">
                        <h3 class="font-semibold text-xl text-gray-800">{{ auth()->user()->first_name }} {{ auth()->user()->surname }}</h3>
                        <button @click.stop="profileModal = true; editMode = true; initializeProfileForm()" 
                                title="Edit Profile"
                                class="profile-edit-icon w-8 h-8 rounded-full bg-purple-100 hover:bg-purple-200 text-purple-600 hover:text-purple-700 transition-all duration-200 flex items-center justify-center hover:scale-110 group border border-purple-200 hover:border-purple-300 shadow-sm hover:shadow-md">
                            <i class="fas fa-edit text-sm group-hover:rotate-12 transition-transform duration-200"></i>
                        </button>
                    </div>
                    <p class="text-gray-600">{{ auth()->user()->department }} - {{ auth()->user()->year_level }}</p>
                    <p class="text-sm text-purple-600 mt-1">Click to view profile</p>
                </div>
            </div>
            <div class="text-purple-500 transform transition-transform group-hover:translate-x-1">
                <i class="fas fa-chevron-right"></i>
            </div>
        </div>

        <main class="bulletin-board p-6 md:p-8">
            <div class="row-layout mt-6">
                <!-- FIRST ROW: Announcements Section -->
                <div class="section p-5 pin">
                    <div class="section-header">
                        <i class="fas fa-bullhorn mr-2 text-blue-500 text-xl"></i>
                        <h2 class="text-xl font-semibold">Announcements</h2>
                        <span class="ml-auto text-sm text-gray-500">{{ $totalAnnouncements }} total</span>
                    </div>
                    <div class="section-content">
                        @forelse($announcements as $announcement)
                            <div class="announcement-item item-hover announcement-hover p-4 rounded cursor-pointer" 
                                 @click="activeModal = {
                                    title: '{{ addslashes($announcement->title) }}', 
                                    body: '{{ addslashes($announcement->content) }}',
                                    category: 'announcement',
                                    contentId: {{ $announcement->id }},
                                    date: 'Posted: {{ $announcement->created_at->format('M d, Y') }}',
                                    media: '{{ $announcement->hasMedia }}',
                                    mediaUrl: '{{ $announcement->mediaUrl ?? '' }}',
                                    allImageUrls: {{ json_encode($announcement->allImageUrls ?? []) }},
                                    allVideoUrls: {{ json_encode($announcement->allVideoUrls ?? []) }},
                                    videoUrl: '{{ $announcement->hasMedia === 'both' && $announcement->allVideoUrls ? $announcement->allVideoUrls[0] : ($announcement->hasMedia === 'video' ? $announcement->mediaUrl : '') }}',
                                    publisher: '{{ $announcement->admin->role === 'superadmin' ? 'MCC Administration' : ($announcement->admin->role === 'department_admin' ? $announcement->admin->department_display . ' Department' : ($announcement->admin->role === 'office_admin' ? $announcement->admin->office_display : $announcement->admin->username)) }}'
                                 }">
                                @if($announcement->hasMedia === 'image' || $announcement->hasMedia === 'both')
                                    <img src="{{ $announcement->mediaUrl }}" 
                                         alt="{{ $announcement->title }}" class="item-media">
                                @elseif($announcement->hasMedia === 'video')
                                    <div class="video-container">
                                        <video class="item-media" muted>
                                            <source src="{{ $announcement->mediaUrl }}" type="video/mp4">
                                        </video>
                                        <div class="play-button">
                                            <i class="fas fa-play"></i>
                                        </div>
                                    </div>
                                @else
                                    <div class="item-media bg-gray-100 flex items-center justify-center">
                                        <i class="fas fa-bullhorn text-gray-400 text-4xl"></i>
                                    </div>
                                @endif
                                <span class="font-medium block">{{ $announcement->title }}</span>
                                <p class="text-sm text-gray-500 mt-1">Posted: {{ $announcement->created_at->format('M d, Y') }}</p>
                                @if($announcement->hasMedia === 'both')
                                    <div class="flex items-center mt-1 text-xs text-blue-600">
                                        <i class="fas fa-images mr-1"></i>
                                        <i class="fas fa-video mr-1"></i>
                                        <span>Images & Videos</span>
                                    </div>
                                @endif
                                <div class="flex items-center mt-2 text-xs text-gray-600">
                                    <i class="fas fa-user-shield mr-1"></i>
                                    <span>
                                        @if($announcement->admin->role === 'superadmin')
                                            MCC Administration
                                        @elseif($announcement->admin->role === 'department_admin')
                                            {{ $announcement->admin->department_display }} Department
                                        @elseif($announcement->admin->role === 'office_admin')
                                            {{ $announcement->admin->office_display }}
                                        @else
                                            {{ $announcement->admin->username }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-8">
                                <i class="fas fa-bullhorn text-gray-400 text-4xl mb-4"></i>
                                <p class="text-gray-500">No announcements available</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- SECOND ROW: Events Section -->
                <div class="section p-5 pin">
                    <div class="section-header">
                        <i class="fas fa-calendar-alt mr-2 text-green-500 text-xl"></i>
                        <h2 class="text-xl font-semibold">Events</h2>
                        <span class="ml-auto text-sm text-gray-500">{{ $totalEvents }} total</span>
                    </div>
                    <div class="section-content">
                        @forelse($events as $event)
                            <div class="announcement-item item-hover event-hover p-4 rounded cursor-pointer" 
                                 @click="activeModal = {
                                    title: '{{ addslashes($event->title) }}', 
                                    body: '{{ addslashes($event->description) }}',
                                    category: 'event',
                                    contentId: {{ $event->id }},
                                    date: 'Date: {{ $event->event_date ? $event->event_date->format('M d, Y') : 'TBD' }}',
                                    location: 'Location: {{ $event->location ?? 'No location specified' }}',
                                    media: '{{ $event->hasMedia }}',
                                    mediaUrl: '{{ $event->mediaUrl ?? '' }}',
                                    allImageUrls: {{ json_encode($event->allImageUrls ?? []) }},
                                    allVideoUrls: {{ json_encode($event->allVideoUrls ?? []) }},
                                    videoUrl: '{{ $event->hasMedia === 'both' && $event->allVideoUrls ? $event->allVideoUrls[0] : ($event->hasMedia === 'video' ? $event->mediaUrl : '') }}',
                                    publisher: '{{ $event->admin->role === 'superadmin' ? 'MCC Administration' : ($event->admin->role === 'department_admin' ? $event->admin->department_display . ' Department' : ($event->admin->role === 'office_admin' ? $event->admin->office_display : $event->admin->username)) }}'
                                 }">
                                @if($event->hasMedia === 'image' || $event->hasMedia === 'both')
                                    <img src="{{ $event->mediaUrl }}" 
                                         alt="{{ $event->title }}" class="item-media">
                                @elseif($event->hasMedia === 'video')
                                    <div class="video-container">
                                        <video class="item-media" muted>
                                            <source src="{{ $event->mediaUrl }}" type="video/mp4">
                                        </video>
                                        <div class="play-button">
                                            <i class="fas fa-play"></i>
                                        </div>
                                    </div>
                                @else
                                    <div class="item-media bg-gray-100 flex items-center justify-center">
                                        <i class="fas fa-calendar-alt text-gray-400 text-4xl"></i>
                                    </div>
                                @endif
                                <span class="font-medium block">{{ $event->title }}</span>
                                <p class="text-sm text-gray-500 mt-1">Date: {{ $event->event_date ? $event->event_date->format('M d, Y') : 'TBD' }}</p>
                                @if($event->location)
                                    <p class="text-sm text-green-600 mt-1 flex items-center">
                                        <i class="fas fa-map-marker-alt mr-1 text-xs"></i>
                                        {{ $event->location }}
                                    </p>
                                @endif
                                @if($event->hasMedia === 'both')
                                    <div class="flex items-center mt-1 text-xs text-green-600">
                                        <i class="fas fa-images mr-1"></i>
                                        <i class="fas fa-video mr-1"></i>
                                        <span>Images & Videos</span>
                                    </div>
                                @endif
                                <div class="flex items-center mt-2 text-xs text-gray-600">
                                    <i class="fas fa-user-shield mr-1"></i>
                                    <span>
                                        @if($event->admin->role === 'superadmin')
                                            MCC Administration
                                        @elseif($event->admin->role === 'department_admin')
                                            {{ $event->admin->department_display }} Department
                                        @elseif($event->admin->role === 'office_admin')
                                            {{ $event->admin->office_display }}
                                        @else
                                            {{ $event->admin->username }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-8">
                                <i class="fas fa-calendar-alt text-gray-400 text-4xl mb-4"></i>
                                <p class="text-gray-500">No events available</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- THIRD ROW: News Section -->
                <div class="section p-5 pin">
                    <div class="section-header">
                        <i class="fas fa-newspaper mr-2 text-red-500 text-xl"></i>
                        <h2 class="text-xl font-semibold">News</h2>
                        <span class="ml-auto text-sm text-gray-500">{{ $totalNews }} total</span>
                    </div>
                    <div class="section-content">
                        @forelse($news as $article)
                            <div class="announcement-item item-hover news-hover p-4 rounded cursor-pointer" 
                                 @click="activeModal = {
                                    title: '{{ addslashes($article->title) }}', 
                                    body: '{{ addslashes($article->content) }}',
                                    category: 'news',
                                    contentId: {{ $article->id }},
                                    date: 'Published: {{ $article->created_at->format('M d, Y') }}',
                                    media: '{{ $article->hasMedia }}',
                                    mediaUrl: '{{ $article->mediaUrl ?? '' }}',
                                    allImageUrls: {{ json_encode($article->allImageUrls ?? []) }},
                                    allVideoUrls: {{ json_encode($article->allVideoUrls ?? []) }},
                                    videoUrl: '{{ $article->hasMedia === 'both' && $article->allVideoUrls ? $article->allVideoUrls[0] : ($article->hasMedia === 'video' ? $article->mediaUrl : '') }}',
                                    publisher: '{{ $article->admin->role === 'superadmin' ? 'MCC Administration' : ($article->admin->role === 'department_admin' ? $article->admin->department_display . ' Department' : ($article->admin->role === 'office_admin' ? $article->admin->office_display : $article->admin->username)) }}'
                                 }">
                                @if($article->hasMedia === 'image' || $article->hasMedia === 'both')
                                    <img src="{{ $article->mediaUrl }}" 
                                         alt="{{ $article->title }}" class="item-media">
                                @elseif($article->hasMedia === 'video')
                                    <div class="video-container">
                                        <video class="item-media" muted>
                                            <source src="{{ $article->mediaUrl }}" type="video/mp4">
                                        </video>
                                        <div class="play-button">
                                            <i class="fas fa-play"></i>
                                        </div>
                                    </div>
                                @else
                                    <div class="item-media bg-gray-100 flex items-center justify-center">
                                        <i class="fas fa-newspaper text-gray-400 text-4xl"></i>
                                    </div>
                                @endif
                                <span class="font-medium block">{{ $article->title }}</span>
                                <p class="text-sm text-gray-500 mt-1">Published: {{ $article->created_at->format('M d, Y') }}</p>
                                @if($article->hasMedia === 'both')
                                    <div class="flex items-center mt-1 text-xs text-red-600">
                                        <i class="fas fa-images mr-1"></i>
                                        <i class="fas fa-video mr-1"></i>
                                        <span>Images & Videos</span>
                                    </div>
                                @endif
                                <div class="flex items-center mt-2 text-xs text-gray-600">
                                    <i class="fas fa-user-shield mr-1"></i>
                                    <span>
                                        @if($article->admin->role === 'superadmin')
                                            MCC Administration
                                        @elseif($article->admin->role === 'department_admin')
                                            {{ $article->admin->department_display }} Department
                                        @elseif($article->admin->role === 'office_admin')
                                            {{ $article->admin->office_display }}
                                        @else
                                            {{ $article->admin->username }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-8">
                                <i class="fas fa-newspaper text-gray-400 text-4xl mb-4"></i>
                                <p class="text-gray-500">No news available</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </main>

        <!-- Content Modal -->
        <div x-show="activeModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center overflow-y-auto z-50 p-4"
             @click.self="activeModal = null; playingVideo = null; comments = []; replyingTo = null; replyContent = ''; commentContent = ''" 
             @keydown.escape="activeModal = null; playingVideo = null; comments = []; replyingTo = null; replyContent = ''; commentContent = ''">
            <div class="modal-container overflow-hidden flex flex-col mt-6 active"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 @click.stop>
                <div class="p-6 border-b border-gray-200 flex items-center justify-between modal-header">
                    <h3 class="text-2xl font-bold text-gray-800" x-text="activeModal?.title"></h3>
                    <button class="text-gray-400 hover:text-gray-600 transition-colors modal-close-btn" @click="activeModal = null; playingVideo = null; comments = []; replyingTo = null; replyContent = ''; commentContent = ''">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="p-6 modal-content-area pb-28">
                    <template x-if="activeModal">
                        <div>
                            <span class="modal-category" :class="{
                                'category-announcement': activeModal.category === 'announcement',
                                'category-event': activeModal.category === 'event',
                                'category-news': activeModal.category === 'news'
                            }" x-text="activeModal.category.charAt(0).toUpperCase() + activeModal.category.slice(1)"></span>
                            
                            <!-- Single or Multiple Images Display -->
                            <template x-if="activeModal.media === 'image'">
                                <div>
                                    <template x-if="activeModal.allImageUrls && activeModal.allImageUrls.length > 1">
                                        <!-- Multiple Images Grid -->
                                        <div class="modal-media-container">
                                            <template x-for="(imageUrl, index) in activeModal.allImageUrls.slice(0, 2)" :key="index">
                                                <div class="modal-media-item">
                                                    <img :src="imageUrl" 
                                                         :alt="activeModal.title + ' - Image ' + (index + 1)"
                                                         class="cursor-pointer hover:opacity-80 transition-opacity"
                                                         @click="selectedImage = imageUrl; imageModal = true">
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="!activeModal.allImageUrls || activeModal.allImageUrls.length <= 1">
                                        <!-- Single Image -->
                                        <div class="w-full flex justify-center">
                                            <img :src="activeModal.mediaUrl" 
                                                 :alt="activeModal.title" 
                                                 class="modal-single-image cursor-pointer hover:opacity-80 transition-opacity"
                                                 @click="selectedImage = activeModal.mediaUrl; imageModal = true">
                                        </div>
                                    </template>
                                </div>
                            </template>
                            
                            <!-- Single Video Display -->
                            <template x-if="activeModal.media === 'video'">
                                <div class="relative">
                                    <video :src="activeModal.mediaUrl" 
                                           controls 
                                           class="modal-single-video"
                                           x-ref="modalVideo"
                                           preload="metadata"
                                           playsinline>
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            </template>
                            
                            <!-- Both Images and Videos - Enhanced Grid Layout -->
                            <template x-if="activeModal.media === 'both'">
                                <div>
                                    <!-- Images Section - Render single image centered when exactly one, otherwise grid up to 2 -->
                                    <template x-if="activeModal.allImageUrls && activeModal.allImageUrls.length === 1">
                                        <div class="w-full flex justify-center mb-4">
                                            <img :src="activeModal.allImageUrls[0]" 
                                                 :alt="activeModal.title + ' - Image 1'"
                                                 class="modal-single-image cursor-pointer hover:opacity-80 transition-opacity"
                                                 @click="selectedImage = activeModal.allImageUrls[0]; imageModal = true">
                                        </div>
                                    </template>
                                    <template x-if="activeModal.allImageUrls && activeModal.allImageUrls.length > 1">
                                        <div class="modal-media-container">
                                            <template x-for="(imageUrl, index) in activeModal.allImageUrls.slice(0, 2)" :key="index">
                                                <div class="modal-media-item">
                                                    <img :src="imageUrl" 
                                                         :alt="activeModal.title + ' - Image ' + (index + 1)"
                                                         class="cursor-pointer hover:opacity-80 transition-opacity"
                                                         @click="selectedImage = imageUrl; imageModal = true">
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    
                                    <!-- Videos Section -->
                                    <div x-show="activeModal.allVideoUrls && activeModal.allVideoUrls.length > 0">
                                        <template x-for="(videoUrl, index) in activeModal.allVideoUrls.slice(0, 1)" :key="index">
                                            <div class="modal-video-container">
                                                <video :src="videoUrl" 
                                                       controls 
                                                       x-ref="modalVideo"
                                                       preload="metadata"
                                                       playsinline>
                                                    Your browser does not support the video tag.
                                                </video>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                            
                            <p class="modal-date" x-text="activeModal.date"></p>
                            <template x-if="activeModal.category === 'event' && activeModal.location">
                                <div class="modal-location-container">
                                    <p class="modal-location" x-text="activeModal.location"></p>
                                </div>
                            </template>
                            <div class="modal-content" x-text="activeModal.body"></div>
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-user-shield mr-2"></i>
                                    <span x-text="activeModal.publisher"></span>
                                </div>
                            </div>
                            
                            <!-- Comments Section -->
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-lg font-semibold text-gray-800">
                                        <i class="fas fa-comments mr-2 text-blue-500"></i>
                                        Comments
                                        <span x-show="comments.length > 0" class="ml-2 text-sm font-normal text-gray-500" x-text="'(' + comments.length + ')'"></span>
                                    </h4>
                                </div>
                                
                                <!-- Comments Container -->
                                <div class="space-y-4">
                                    <!-- Comment Form -->
                                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                        <form @submit.prevent="submitComment()">
                                            <div class="flex items-start space-x-3">
                                                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                                    {{ substr(auth()->user()->first_name, 0, 1) }}{{ substr(auth()->user()->surname, 0, 1) }}
                                                </div>
                                                <div class="flex-1">
                                                    <textarea x-model="commentContent" 
                                                              placeholder="Share your thoughts..."
                                                              class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                                              rows="3"
                                                              maxlength="1000"
                                                              :disabled="submittingComment"
                                                              required></textarea>
                                                    <div class="flex justify-between items-center mt-2">
                                                        <div class="text-xs text-gray-500">
                                                            <span x-text="commentContent.length"></span>/1000 characters
                                                        </div>
                                                        <button type="submit" 
                                                                :disabled="submittingComment || !commentContent.trim()"
                                                                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors flex items-center text-sm">
                                                            <i class="fas fa-paper-plane mr-2" x-show="!submittingComment"></i>
                                                            <i class="fas fa-spinner fa-spin mr-2" x-show="submittingComment"></i>
                                                            <span x-text="submittingComment ? 'Posting...' : 'Post Comment'"></span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    
                                    <!-- Comments List -->
                                    <div class="space-y-3" x-show="comments.length > 0">
                                        <template x-for="comment in comments" :key="comment.id">
                                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                                <div class="flex items-start space-x-3">
                                                    <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-pink-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                                        <span x-text="comment.user.first_name.charAt(0) + comment.user.surname.charAt(0)"></span>
                                                    </div>
                                                    <div class="flex-1">
                                                        <div class="flex items-center justify-between mb-1">
                                                            <div class="flex items-center space-x-2">
                                                                <span class="font-semibold text-blue-600 text-sm" x-text="comment.user.first_name + '_' + comment.user.surname.toLowerCase()"></span>
                                                                <span class="text-xs text-gray-500" x-text="comment.time_ago"></span>
                                                            </div>
                                                            <div class="flex items-center space-x-2">
                                                                <button @click="deleteComment(comment.id)" 
                                                                        class="text-xs text-gray-500 hover:text-red-600 transition-colors"
                                                                        x-show="comment.user_id === {{ auth()->user()->id }}">
                                                                    Remove
                                                                </button>
                                                                <span class="text-gray-300" x-show="comment.user_id === {{ auth()->user()->id }}">•</span>
                                                                <button @click="startReply(comment.id)" 
                                                                        class="text-xs text-gray-500 hover:text-blue-600 transition-colors">
                                                                    Reply
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="text-gray-800 text-sm mb-2" x-text="comment.content"></div>
                                                        
                                                        <!-- Replies -->
                                                        <div x-show="comment.replies && comment.replies.length > 0" class="mt-3 ml-4 space-y-2">
                                                            <template x-for="reply in comment.replies" :key="reply.id">
                                                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                                                    <div class="flex items-start space-x-2">
                                                                        <div class="w-8 h-8 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-white font-semibold text-xs">
                                                                            <span x-text="reply.user.first_name.charAt(0) + reply.user.surname.charAt(0)"></span>
                                                                        </div>
                                                                        <div class="flex-1">
                                                                            <div class="flex items-center justify-between mb-1">
                                                                                <div class="flex items-center space-x-2">
                                                                                    <span class="font-semibold text-green-600 text-xs" x-text="reply.user.first_name + '_' + reply.user.surname.toLowerCase()"></span>
                                                                                    <span class="text-xs text-gray-500" x-text="reply.time_ago"></span>
                                                                                </div>
                                                                                <div class="flex items-center space-x-2">
                                                                                    <button @click="deleteComment(reply.id)" 
                                                                                            class="text-xs text-gray-500 hover:text-red-600 transition-colors"
                                                                                            x-show="reply.user_id === {{ auth()->user()->id }}">
                                                                                        Remove
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                            <div class="text-gray-700 text-xs" x-text="reply.content"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                        
                                                        <!-- Reply Form (shown when replying to this comment) -->
                                                        <div x-show="replyingTo === comment.id" x-transition class="mt-4 p-3 bg-gray-50 rounded-lg border">
                                                            <form @submit.prevent="submitReply(comment.id)">
                                                                <div class="flex items-start space-x-3">
                                                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-xs">
                                                                        {{ substr(auth()->user()->first_name, 0, 1) }}{{ substr(auth()->user()->surname, 0, 1) }}
                                                                    </div>
                                                                    <div class="flex-1">
                                                                        <textarea x-model="replyContent" 
                                                                                  placeholder="Write a reply..."
                                                                                  class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none text-sm"
                                                                                  rows="2"
                                                                                  maxlength="1000"
                                                                                  :disabled="submittingReply"
                                                                                  required></textarea>
                                                                        <div class="flex justify-between items-center mt-2">
                                                                            <div class="text-xs text-gray-500">
                                                                                <span x-text="replyContent.length"></span>/1000 characters
                                                                            </div>
                                                                            <div class="flex space-x-2">
                                                                                <button type="button" 
                                                                                        @click="cancelReply()"
                                                                                        class="px-3 py-1 text-xs text-gray-600 hover:text-gray-800 transition-colors">
                                                                                    Cancel
                                                                                </button>
                                                                                <button type="submit" 
                                                                                        :disabled="submittingReply || !replyContent.trim()"
                                                                                        class="px-3 py-1 bg-blue-500 text-white rounded text-xs hover:bg-blue-600 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors">
                                                                                    <span x-text="submittingReply ? 'Posting...' : 'Reply'"></span>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    
                                    <!-- No Comments Message -->
                                    <div x-show="comments.length === 0 && !loadingComments" class="text-center py-8 text-gray-500">
                                        <i class="fas fa-comment-slash text-4xl mb-4"></i>
                                        <p>No comments yet. Be the first to comment!</p>
                                    </div>
                                    
                                    <!-- Loading State -->
                                    <div x-show="loadingComments" class="text-center py-8">
                                        <i class="fas fa-spinner fa-spin text-2xl text-blue-500 mb-4"></i>
                                        <p class="text-gray-500">Loading comments...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="p-6 border-t border-gray-200 flex justify-end">
                    <button class="px-6 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-900 transition-colors" 
                            @click="activeModal = null; playingVideo = null; comments = []; replyingTo = null; replyContent = ''; commentContent = ''">
                        Close
                    </button>
                </div>
            </div>
        </div>

        <!-- Enhanced Profile Modal -->
        <div x-show="profileModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50 p-4" 
             @keydown.escape="profileModal = false; editMode = false; resetProfileForm()">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[95vh] overflow-hidden"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 @click.stop
                 x-init="$watch('editMode', () => { $nextTick(() => { if ($refs.modalContent) $refs.modalContent.scrollTop = 0; }); })">
                
                <!-- Header -->
                <div class="relative p-6 bg-gradient-to-r from-purple-600 to-blue-600 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold">My Profile</h3>
                            <p class="text-purple-100 text-sm">Manage your account information</p>
                        </div>
                        <button @click="profileModal = false; editMode = false; resetProfileForm()" 
                                class="w-10 h-10 rounded-full bg-white bg-opacity-20 hover:bg-opacity-30 transition-all duration-200 flex items-center justify-center">
                            <i class="fas fa-times text-white"></i>
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6 overflow-y-auto max-h-[calc(95vh-200px)]" x-ref="modalContent">
                    <!-- Profile Picture Section -->
                    <div class="flex flex-col items-center mb-8">
                        <div class="relative group">
                            <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-purple-200 shadow-lg bg-gradient-to-br from-purple-400 to-purple-600">
                                @if(auth()->user()->hasProfilePicture)
                                    <img x-ref="profileImage" 
                                         src="{{ auth()->user()->profilePictureUrl }}" 
                                         alt="Profile Picture" 
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-white font-bold text-3xl">
                                        {{ auth()->user()->initials }}
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Upload/Change Picture Button -->
                            <div class="absolute inset-0 rounded-full bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center cursor-pointer"
                                 @click="$refs.profilePictureInput.click()">
                                <div class="text-center text-white">
                                    <i class="fas fa-camera text-2xl mb-1"></i>
                                    <p class="text-xs">Change Photo</p>
                                </div>
                            </div>
                            
                            <!-- Hidden File Input -->
                            <input type="file" 
                                   x-ref="profilePictureInput" 
                                   accept="image/jpeg,image/png,image/jpg" 
                                   @change="handleProfilePictureChange($event)" 
                                   class="hidden">
                        </div>
                        
                        <!-- Profile Picture Actions -->
                        <div class="flex space-x-3 mt-4 justify-center">
                            <button @click="$refs.profilePictureInput.click()" 
                                    class="profile-action-btn w-12 h-12 bg-green-500 text-white rounded-full hover:bg-green-600 transition-all duration-300 flex items-center justify-center shadow-lg hover:shadow-xl transform hover:scale-110 group"
                                    title="Upload Photo">
                                <i class="fas fa-camera text-lg group-hover:scale-110 transition-transform duration-200"></i>
                            </button>
                            @if(auth()->user()->hasProfilePicture)
                                <button @click="removeProfilePicture()" 
                                        class="profile-action-btn w-12 h-12 bg-red-500 text-white rounded-full hover:bg-red-600 transition-all duration-300 flex items-center justify-center shadow-lg hover:shadow-xl transform hover:scale-110 group"
                                        title="Remove Photo">
                                    <i class="fas fa-trash text-lg group-hover:scale-110 transition-transform duration-200"></i>
                                </button>
                            @endif
                        </div>
                        
                        <h3 class="font-bold text-xl text-gray-800 mt-4" x-text="editMode ? 'Edit Profile' : '{{ auth()->user()->first_name }} {{ auth()->user()->surname }}'"></h3>
                        <p class="text-gray-600" x-show="!editMode">{{ auth()->user()->department }} - {{ auth()->user()->year_level }}</p>
                    </div>
                    
                    <!-- Profile Information -->
                    <div x-show="!editMode" class="space-y-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center mb-3">
                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-envelope text-purple-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-500">MS365 Account</p>
                                    <p class="text-gray-800">{{ auth()->user()->email }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center mb-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-building text-blue-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-500">Department</p>
                                    <p class="text-gray-800">{{ auth()->user()->department }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center mb-3">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-graduation-cap text-green-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-500">Year Level</p>
                                    <p class="text-gray-800">{{ auth()->user()->year_level }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center mb-3">
                                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-calendar-alt text-orange-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-500">Member Since</p>
                                    <p class="text-gray-800">{{ auth()->user()->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Edit Profile Form -->
                    <div x-show="editMode" x-transition class="space-y-4">
                        <form @submit.prevent="updateProfile()">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                    <input type="text" 
                                           x-model="profileForm.first_name" 
                                           @input="profileForm.first_name = validateNameInput(profileForm.first_name)"
                                           pattern="^[a-zA-Z]+([a-zA-Z\s]*[a-zA-Z])?$"
                                           title="Only letters and spaces are allowed. No numbers or symbols."
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                           required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                    <input type="text" 
                                           x-model="profileForm.surname" 
                                           @input="profileForm.surname = validateNameWithHyphen(profileForm.surname)"
                                           pattern="^[a-zA-Z]+([a-zA-Z\s]*[a-zA-Z]|[a-zA-Z]*-[a-zA-Z]+)*$"
                                           title="Only letters, spaces, and hyphens in the middle of names are allowed (e.g., Bayonon-on)"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                           required>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Middle Name (Optional)</label>
                                <input type="text" 
                                       x-model="profileForm.middle_name" 
                                       @input="profileForm.middle_name = validateNameWithHyphen(profileForm.middle_name)"
                                       pattern="^$|^[a-zA-Z]+([a-zA-Z\s]*[a-zA-Z]|[a-zA-Z]*-[a-zA-Z]+)*$"
                                       title="Only letters, spaces, and hyphens in the middle of names are allowed (e.g., Bayonon-on)"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                                <select x-model="profileForm.department" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                        required>
                                    <option value="Bachelor of Science in Information Technology">Bachelor of Science in Information Technology</option>
                                    <option value="Bachelor of Science in Business Administration">Bachelor of Science in Business Administration</option>
                                    <option value="Bachelor of Elementary Education">Bachelor of Elementary Education</option>
                                    <option value="Bachelor of Science in Hospitality Management">Bachelor of Science in Hospitality Management</option>
                                    <option value="Bachelor of Secondary Education">Bachelor of Secondary Education</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Year Level</label>
                                <select x-model="profileForm.year_level" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                        required>
                                    <option value="1st Year">1st Year</option>
                                    <option value="2nd Year">2nd Year</option>
                                    <option value="3rd Year">3rd Year</option>
                                    <option value="4th Year">4th Year</option>
                                </select>
                            </div>
                            
                            <div class="flex space-x-3 pt-4">
                                <button type="submit" 
                                        :disabled="updatingProfile"
                                        class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:bg-gray-400 transition-colors flex items-center justify-center">
                                    <i class="fas fa-save mr-2" x-show="!updatingProfile"></i>
                                    <i class="fas fa-spinner fa-spin mr-2" x-show="updatingProfile"></i>
                                    <span x-text="updatingProfile ? 'Saving...' : 'Save Changes'"></span>
                                </button>
                                <button type="button" 
                                        @click="editMode = false; resetProfileForm()" 
                                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="p-6 border-t bg-gradient-to-r from-gray-50 to-gray-100">
                    <div class="flex justify-center items-center">
                        <!-- Logout Button (Icon Only) -->
                        <button @click="logout()" 
                                title="Logout"
                                class="profile-action-btn w-12 h-12 rounded-full bg-gradient-to-r from-red-500 to-red-600 text-white hover:from-red-600 hover:to-red-700 transition-all duration-300 flex items-center justify-center shadow-lg hover:shadow-xl transform hover:scale-110 group">
                            <i class="fas fa-sign-out-alt text-lg group-hover:rotate-12 transition-transform duration-200"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Viewer Modal -->
        <div x-show="imageModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 p-4" 
             @click="imageModal = false; selectedImage = null"
             @keydown.escape="imageModal = false; selectedImage = null">
            <div class="relative max-w-6xl max-h-full overflow-hidden"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 @click.stop>
                <button @click="imageModal = false; selectedImage = null"
                        class="absolute top-4 right-4 w-12 h-12 bg-black bg-opacity-50 text-white rounded-full flex items-center justify-center hover:bg-opacity-70 transition-all z-10">
                    <i class="fas fa-times text-xl"></i>
                </button>
                <img :src="selectedImage" 
                     :alt="'Large view of image'"
                     class="max-w-full max-h-full object-contain rounded-lg shadow-2xl">
            </div>
        </div>
    </div>

    <script>
        // Alpine.js data function
        function dashboardData() {
            return {
                profileModal: false,
                editMode: false,
                profileForm: {
                    first_name: '{{ auth()->user()->first_name }}',
                    middle_name: '{{ auth()->user()->middle_name }}',
                    surname: '{{ auth()->user()->surname }}',
                    department: '',
                    year_level: ''
                },
                updatingProfile: false,
                uploadingPicture: false,
                activeModal: null,
                playingVideo: null,
                imageModal: false,
                selectedImage: null,
                showComments: true,
                commentContent: '',
                comments: [],
                loadingComments: false,
                submittingComment: false,
                replyingTo: null,
                replyContent: '',
                submittingReply: false,
                notificationCount: 0,
                notifications: [],
                showNotifications: false,
                
                // Comments are now always visible, no toggle needed
                
                // Auto-load comments when modal opens and notifications on page load
                init() {
                    this.$watch('activeModal', (newModal) => {
                        if (newModal) {
                            this.loadComments();
                        }
                    });
                    
                    // Load notifications on page load
                    this.loadNotifications();
                    
                    // Refresh notifications every 15 seconds for more responsive updates
                    setInterval(() => {
                        this.loadNotifications();
                    }, 15000);
                    
                    // Also refresh notifications when the page becomes visible (user switches tabs)
                    document.addEventListener('visibilitychange', () => {
                        if (!document.hidden) {
                            this.loadNotifications();
                        }
                    });
                },
                
                loadComments() {
                    if (!this.activeModal) return;
                    
                    // Clear any existing comments and reset state
                    this.comments = [];
                    this.replyingTo = null;
                    this.replyContent = '';
                    this.commentContent = '';
                    
                    this.loadingComments = true;
                    const contentType = this.activeModal.category;
                    const contentId = this.activeModal.contentId;
                    
                    if (!contentId) {
                        this.loadingComments = false;
                        return;
                    }

                    fetch(`/user/content/${contentType}/${contentId}/comments`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.comments = data.comments;
                        } else {
                            console.error('Error loading comments:', data.error);
                            this.comments = [];
                        }
                    })
                    .catch(error => {
                        console.error('Error loading comments:', error);
                        this.comments = [];
                    })
                    .finally(() => {
                        this.loadingComments = false;
                    });
                },
                
                submitComment() {
                    if (!this.activeModal || !this.commentContent.trim()) return;
                    
                    this.submittingComment = true;
                    const contentType = this.activeModal.category;
                    const contentId = this.activeModal.contentId;
                    
                    if (!contentId) {
                        this.submittingComment = false;
                        return;
                    }

                    fetch('/user/comments', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            content: this.commentContent,
                            content_type: contentType,
                            content_id: contentId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.commentContent = '';
                            this.loadComments();
                        } else {
                            alert('Error posting comment: ' + (data.error || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error posting comment:', error);
                        alert('Error posting comment. Please try again.');
                    })
                    .finally(() => {
                        this.submittingComment = false;
                    });
                },
                
                deleteComment(commentId) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this action!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        background: '#ffffff',
                        customClass: {
                            popup: 'swal-popup-custom',
                            title: 'swal-title-custom',
                            content: 'swal-content-custom'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading
                            Swal.fire({
                                title: 'Deleting...',
                                text: 'Please wait while we delete the comment.',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                willOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            
                            fetch(`/user/comments/${commentId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: 'Your comment has been deleted.',
                                        icon: 'success',
                                        confirmButtonColor: '#10b981',
                                        confirmButtonText: 'OK'
                                    });
                                    this.loadComments();
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Error deleting comment: ' + (data.error || 'Unknown error'),
                                        icon: 'error',
                                        confirmButtonColor: '#ef4444',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error deleting comment:', error);
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Error deleting comment. Please try again.',
                                    icon: 'error',
                                    confirmButtonColor: '#ef4444',
                                    confirmButtonText: 'OK'
                                });
                            });
                        }
                    });
                },
                
                startReply(commentId) {
                    this.replyingTo = commentId;
                    this.replyContent = '';
                },
                
                cancelReply() {
                    this.replyingTo = null;
                    this.replyContent = '';
                },
                
                submitReply(parentCommentId) {
                    if (!this.replyContent.trim()) return;
                    
                    this.submittingReply = true;
                    const contentType = this.activeModal.category;
                    const contentId = this.activeModal.contentId;
                    
                    if (!contentId) {
                        this.submittingReply = false;
                        return;
                    }

                    fetch('/user/comments', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            content: this.replyContent,
                            content_type: contentType,
                            content_id: contentId,
                            parent_id: parentCommentId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.replyContent = '';
                            this.replyingTo = null;
                            this.loadComments();
                        } else {
                            alert('Error posting reply: ' + (data.error || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error posting reply:', error);
                        alert('Error posting reply. Please try again.');
                    })
                    .finally(() => {
                        this.submittingReply = false;
                    });
                },
                
                // Notification functions
                toggleNotifications() {
                    this.showNotifications = !this.showNotifications;
                    if (this.showNotifications) {
                        this.loadNotifications();
                    }
                },
                
                handleNotificationClick(notification) {
                    // Mark notification as read
                    this.markNotificationAsRead(notification.id);
                    
                    // Close notification dropdown
                    this.showNotifications = false;
                    
                    // Find and open the corresponding content
                    this.openContentFromNotification(notification);
                },
                
                markNotificationAsRead(notificationId) {
                    fetch(`/user/notifications/${notificationId}/read`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update notification count
                            this.notificationCount = data.unread_count || 0;
                            
                            // Update the notification in the list
                            const notification = this.notifications.find(n => n.id === notificationId);
                            if (notification) {
                                notification.is_read = true;
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error marking notification as read:', error);
                    });
                },
                
                openContentFromNotification(notification) {
                    // Extract content type and ID from notification
                    const contentType = notification.type; // 'announcement', 'event', or 'news'
                    const contentId = notification.content_id;
                    
                    if (!contentId) {
                        console.error('No content ID found in notification');
                        return;
                    }
                    
                    // Fetch the content details and open modal
                    this.fetchAndOpenContent(contentType, contentId);
                },
                
                fetchAndOpenContent(contentType, contentId) {
                    // Determine the correct endpoint based on content type
                    let endpoint = '';
                    switch(contentType) {
                        case 'announcement':
                            endpoint = `/user/content/announcement/${contentId}`;
                            break;
                        case 'event':
                            endpoint = `/user/content/event/${contentId}`;
                            break;
                        case 'news':
                            endpoint = `/user/content/news/${contentId}`;
                            break;
                        default:
                            console.error('Unknown content type:', contentType);
                            return;
                    }
                    
                    fetch(endpoint, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.content) {
                            const content = data.content;
                            
                            // Set up the modal data
                            this.activeModal = {
                                title: content.title,
                                body: content.content || content.description,
                                category: contentType,
                                contentId: content.id,
                                date: contentType === 'event' 
                                    ? `Date: ${content.event_date ? new Date(content.event_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'TBD'}`
                                    : `Posted: ${new Date(content.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`,
                                media: content.hasMedia || 'none',
                                mediaUrl: content.mediaUrl ? `{{ asset('storage/') }}/${content.mediaUrl}` : '',
                                allImageUrls: content.allImageUrls || [],
                                allVideoUrls: content.allVideoUrls || [],
                                videoUrl: content.hasMedia === 'both' && content.allVideoUrls 
                                    ? `{{ asset('storage/') }}/${content.allVideoUrls[0]}` 
                                    : (content.hasMedia === 'video' ? `{{ asset('storage/') }}/${content.mediaUrl}` : ''),
                                publisher: content.admin.role === 'superadmin' 
                                    ? 'MCC Administration' 
                                    : (content.admin.role === 'department_admin' 
                                        ? `${content.admin.department_display} Department` 
                                        : (content.admin.role === 'office_admin' 
                                            ? content.admin.office_display 
                                            : content.admin.username))
                            };
                        } else {
                            console.error('Error fetching content:', data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching content:', error);
                    });
                },
                
                removeNotification(notificationId) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this action!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, remove it!',
                        cancelButtonText: 'Cancel',
                        background: '#ffffff',
                        customClass: {
                            popup: 'swal-popup-custom',
                            title: 'swal-title-custom',
                            content: 'swal-content-custom'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading
                            Swal.fire({
                                title: 'Removing...',
                                text: 'Please wait while we remove the notification.',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                willOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            
                            fetch(`/user/notifications/${notificationId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Remove notification from the list
                                    this.notifications = this.notifications.filter(n => n.id !== notificationId);
                                    
                                    // Update notification count
                                    this.notificationCount = Math.max(0, this.notificationCount - 1);
                                    
                                    Swal.fire({
                                        title: 'Removed!',
                                        text: 'The notification has been removed.',
                                        icon: 'success',
                                        confirmButtonColor: '#10b981',
                                        confirmButtonText: 'OK'
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Error removing notification: ' + (data.error || 'Unknown error'),
                                        icon: 'error',
                                        confirmButtonColor: '#ef4444',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error removing notification:', error);
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Error removing notification. Please try again.',
                                    icon: 'error',
                                    confirmButtonColor: '#ef4444',
                                    confirmButtonText: 'OK'
                                });
                            });
                        }
                    });
                },
                
                loadNotifications() {
                    fetch('/user/notifications', {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.notifications !== undefined) {
                            this.notifications = data.notifications;
                            this.notificationCount = data.unread_count || 0;
                        }
                    })
                    .catch(error => {
                        console.error('Error loading notifications:', error);
                    });
                },
                
                // Name validation functions
                validateNameInput(value) {
                    // Remove all numbers and symbols, keep only letters and spaces
                    return value.replace(/[^a-zA-Z\s]/g, '');
                },
                
                validateNameWithHyphen(value) {
                    // Remove all numbers and symbols except hyphens
                    let cleaned = value.replace(/[^a-zA-Z\s\-]/g, '');
                    
                    // Ensure hyphens are only in the middle (not at start or end)
                    // Remove hyphens at the beginning
                    cleaned = cleaned.replace(/^-+/, '');
                    // Remove hyphens at the end
                    cleaned = cleaned.replace(/-+$/, '');
                    // Replace multiple consecutive hyphens with single hyphen
                    cleaned = cleaned.replace(/-{2,}/g, '-');
                    
                    return cleaned;
                },
                
                // Profile management functions
                initializeProfileForm() {
                    // Convert short codes to full department names if needed
                    let department = '{{ auth()->user()->department }}';
                    const shortToFullDepartmentMap = {
                        'BSIT': 'Bachelor of Science in Information Technology',
                        'BSBA': 'Bachelor of Science in Business Administration',
                        'BEED': 'Bachelor of Elementary Education',
                        'BSHM': 'Bachelor of Science in Hospitality Management',
                        'BSED': 'Bachelor of Secondary Education'
                    };
                    
                    // Convert year level to proper case
                    let yearLevel = '{{ auth()->user()->year_level }}';
                    const yearLevelMap = {
                        '1st year': '1st Year',
                        '2nd year': '2nd Year',
                        '3rd year': '3rd Year',
                        '4th year': '4th Year'
                    };
                    
                    this.profileForm = {
                        first_name: '{{ auth()->user()->first_name }}',
                        middle_name: '{{ auth()->user()->middle_name }}',
                        surname: '{{ auth()->user()->surname }}',
                        department: shortToFullDepartmentMap[department] || department,
                        year_level: yearLevelMap[yearLevel] || yearLevel
                    };
                    // Reset scroll position when entering edit mode
                    this.$nextTick(() => {
                        if (this.$refs.modalContent) {
                            this.$refs.modalContent.scrollTop = 0;
                        }
                    });
                },
                
                resetProfileForm() {
                    this.initializeProfileForm();
                    // Reset scroll position when canceling edit mode
                    this.$nextTick(() => {
                        if (this.$refs.modalContent) {
                            this.$refs.modalContent.scrollTop = 0;
                        }
                    });
                },
                
                updateProfile() {
                    if (this.updatingProfile) return;
                    
                    this.updatingProfile = true;
                    
                    fetch('/user/profile/update', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(this.profileForm)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#10b981',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                this.editMode = false;
                                // Reload page to reflect changes
                                window.location.reload();
                            });
                        } else {
                            let errorMessage = 'Error updating profile.';
                            if (data.errors) {
                                errorMessage = Object.values(data.errors).flat().join('\\n');
                            } else if (data.message) {
                                errorMessage = data.message;
                            }
                            
                            Swal.fire({
                                title: 'Error!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonColor: '#ef4444',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error updating profile:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Error updating profile. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#ef4444',
                            confirmButtonText: 'OK'
                        });
                    })
                    .finally(() => {
                        this.updatingProfile = false;
                    });
                },
                
                handleProfilePictureChange(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    
                    // Validate file type
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                    if (!allowedTypes.includes(file.type)) {
                        Swal.fire({
                            title: 'Invalid File Type',
                            text: 'Please select a JPG or PNG image file.',
                            icon: 'error',
                            confirmButtonColor: '#ef4444',
                            confirmButtonText: 'OK'
                        });
                        event.target.value = '';
                        return;
                    }
                    
                    // Validate file size (5MB max)
                    if (file.size > 5 * 1024 * 1024) {
                        Swal.fire({
                            title: 'File Too Large',
                            text: 'Please select an image smaller than 5MB.',
                            icon: 'error',
                            confirmButtonColor: '#ef4444',
                            confirmButtonText: 'OK'
                        });
                        event.target.value = '';
                        return;
                    }
                    
                    // Show preview and upload
                    this.uploadProfilePicture(file);
                },
                
                uploadProfilePicture(file) {
                    if (this.uploadingPicture) return;
                    
                    this.uploadingPicture = true;
                    
                    // Show loading
                    Swal.fire({
                        title: 'Uploading...',
                        text: 'Please wait while we upload your profile picture.',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    const formData = new FormData();
                    formData.append('profile_picture', file);
                    
                    fetch('/user/profile/upload-picture', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#10b981',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Update the profile image in the modal
                                if (this.$refs.profileImage) {
                                    this.$refs.profileImage.src = data.profile_picture_url;
                                }
                                // Reload page to reflect changes everywhere
                                window.location.reload();
                            });
                        } else {
                            let errorMessage = 'Error uploading profile picture.';
                            if (data.errors && data.errors.profile_picture) {
                                errorMessage = data.errors.profile_picture[0];
                            } else if (data.message) {
                                errorMessage = data.message;
                            }
                            
                            Swal.fire({
                                title: 'Error!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonColor: '#ef4444',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error uploading profile picture:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Error uploading profile picture. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#ef4444',
                            confirmButtonText: 'OK'
                        });
                    })
                    .finally(() => {
                        this.uploadingPicture = false;
                    });
                },
                
                removeProfilePicture() {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'This will remove your current profile picture.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, remove it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading
                            Swal.fire({
                                title: 'Removing...',
                                text: 'Please wait while we remove your profile picture.',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                willOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            
                            fetch('/user/profile/remove-picture', {
                                method: 'DELETE',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Removed!',
                                        text: data.message,
                                        icon: 'success',
                                        confirmButtonColor: '#10b981',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        // Reload page to reflect changes
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: data.message || 'Error removing profile picture.',
                                        icon: 'error',
                                        confirmButtonColor: '#ef4444',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error removing profile picture:', error);
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Error removing profile picture. Please try again.',
                                    icon: 'error',
                                    confirmButtonColor: '#ef4444',
                                    confirmButtonText: 'OK'
                                });
                            });
                        }
                    });
                },
                
                // Logout function with SweetAlert
                logout() {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You will be logged out of your account.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, logout!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading
                            Swal.fire({
                                title: 'Logging out...',
                                text: 'Please wait while we log you out.',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                willOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            
                            // Perform logout
                            fetch('/user/logout', {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            })
                            .then(response => {
                                if (response.ok) {
                                    Swal.fire({
                                        title: 'Logged out!',
                                        text: 'You have been successfully logged out.',
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        window.location.href = '/login';
                                    });
                                } else {
                                    throw new Error('Logout failed');
                                }
                            })
                            .catch(error => {
                                console.error('Logout error:', error);
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'There was an error logging out. Please try again.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            });
                        }
                    });
                }
            }
        }

        // Simple video play functionality
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.play-button').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const container = this.closest('.announcement-item');
                    container.click();
                });
            });
        });
    </script>
</body>
</html>
