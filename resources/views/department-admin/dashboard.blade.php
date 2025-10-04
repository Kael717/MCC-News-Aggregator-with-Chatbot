<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Admin Dashboard - {{ $admin->department }} - MCC Portal</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 50%, #e2e8f0 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 320px;
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #2d2d2d 100%);
            color: white;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1000;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.3), 0 0 40px rgba(0, 0, 0, 0.1);
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.02) 50%, transparent 70%);
            animation: shimmer 3s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes shimmer {
            0%, 100% { transform: translateX(-100%); opacity: 0; }
            50% { transform: translateX(100%); opacity: 1; }
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
            color: white;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .sidebar-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #ffffff, #e5e7eb, #ffffff);
            animation: headerShimmer 2s ease-in-out infinite;
        }

        @keyframes headerShimmer {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 1; }
        }

        .sidebar-header h3 {
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #ffffff 0%, #e5e7eb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
            margin: 0;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            letter-spacing: 0.5px;
            line-height: 1.2;
        }

        .sidebar-header h3 i {
            font-size: 1.5rem;
            color: #ffffff;
            background: linear-gradient(135deg, #ffffff 0%, #d1d5db 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }

        .sidebar-header .dept-info {
            font-size: 0.85rem;
            margin-top: 0.5rem;
            opacity: 0.85;
            color: #d1d5db;
            font-weight: 500;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            background: linear-gradient(135deg, #d1d5db 0%, #9ca3af 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
            line-height: 1.3;
        }

        .sidebar-menu {
            list-style: none;
            padding: 1.5rem 0;
        }

        .sidebar-menu li {
            margin: 0.25rem 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 1rem 2rem;
            color: #d1d5db;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            gap: 1rem;
            position: relative;
            border-radius: 0 25px 25px 0;
            margin: 0.25rem 0;
            overflow: hidden;
            letter-spacing: 0.3px;
        }

        .sidebar-menu a::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(135deg, #ffffff, #e5e7eb);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.05) 100%);
            color: white;
            transform: translateX(8px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
        }

        .sidebar-menu a:hover::before,
        .sidebar-menu a.active::before {
            transform: scaleY(1);
        }

        .sidebar-menu a i {
            width: 18px;
            height: 18px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
            flex-shrink: 0;
        }

        .sidebar-menu a:hover i,
        .sidebar-menu a.active i {
            transform: scale(1.2) rotate(5deg);
            color: #ffffff;
        }

        .sidebar-menu a span {
            transition: all 0.3s ease;
            flex: 1;
            text-align: left;
            line-height: 1.4;
        }

        .sidebar-menu a:hover span,
        .sidebar-menu a.active span {
            font-weight: 600;
            letter-spacing: 0.5px;
            color: #ffffff;
        }

        .main-content {
            flex: 1;
            margin-left: 320px;
            padding: 2rem;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 50%, #e2e8f0 100%);
            min-height: 100vh;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 1.5rem 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #000000, #4a4a4a, #000000);
        }

        .header h1 {
            color: #1a1a1a;
            font-size: 2.5rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 0;
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #4a4a4a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            letter-spacing: 0.5px;
            line-height: 1.2;
        }

        .header h1 i {
            color: #000000;
            font-size: 2.8rem;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .logout-btn {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(239, 68, 68, 0.4);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #000000, #4a4a4a);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(31, 38, 135, 0.5);
        }

        .stat-card h3 {
            color: #666;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stat-card .number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .stat-card .change {
            font-size: 0.85rem;
            color: #10b981;
            font-weight: 600;
        }

        .chart-container {
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(156, 163, 175, 0.1);
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            position: relative;
            overflow: hidden;
            animation: chartSlideIn 0.8s ease-out;
            margin-bottom: 2rem;
            padding: 1.5rem;
        }

        @keyframes chartSlideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .chart-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #000000, #4a4a4a);
            border-radius: 16px 16px 0 0;
        }

        .chart-wrapper {
            position: relative;
            height: 320px;
            width: 100%;
        }

        /* Enhanced chart title */
        .chart-container h2 {
            color: #333;
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: linear-gradient(135deg, #000000, #4a4a4a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .chart-container h2 i {
            color: #000000;
            font-size: 1.6rem;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }

        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .quick-action-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            text-align: center;
        }

        .quick-action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(31, 38, 135, 0.5);
            color: #333;
        }

        .quick-action-card .action-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #10b981;
        }

        .quick-action-card h4 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .quick-action-card p {
            font-size: 0.9rem;
            color: #666;
        }

        .department-badge {
            background: linear-gradient(135deg, #000000, #1a1a1a, #4a4a4a);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: 0.75rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2), 0 2px 8px rgba(0, 0, 0, 0.1);
            letter-spacing: 0.3px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }

        .department-badge::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
            animation: badgeShimmer 3s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes badgeShimmer {
            0%, 100% { transform: translateX(-100%); opacity: 0; }
            50% { transform: translateX(100%); opacity: 1; }
        }

        .department-badge i {
            font-size: 1.3rem;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }

        /* Mobile Toggle Button */
        .mobile-toggle {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1001;
            background: linear-gradient(135deg, #000000, #4a4a4a);
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 12px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .mobile-toggle:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        }

        /* Sidebar Overlay for Mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            backdrop-filter: blur(5px);
        }

        @media (max-width: 768px) {
            .mobile-toggle {
                display: block;
            }

            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .sidebar-overlay.active {
                display: block;
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
                padding-top: 4rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
        }

        /* Scrollbar Styling */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #ffffff, #d1d5db);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #f3f4f6, #9ca3af);
        }
    </style>
</head>
<body>
    <!-- Mobile Toggle Button -->
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" onclick="closeSidebar()"></div>

    <div class="dashboard-container">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-building"></i> Department Admin</h3>
                <div class="dept-info">{{ $admin->department }} Department</div>
            </div>
            <ul class="sidebar-menu">
                <li><a href="{{ route('department-admin.dashboard') }}" class="active">
                    <i class="fas fa-chart-pie"></i> <span>Dashboard</span>
                </a></li>
                <li><a href="{{ route('department-admin.announcements.index') }}">
                    <i class="fas fa-bullhorn"></i> <span>Announcements</span>
                </a></li>
                <li><a href="{{ route('department-admin.events.index') }}">
                    <i class="fas fa-calendar-alt"></i> <span>Events</span>
                </a></li>
                <li><a href="{{ route('department-admin.news.index') }}">
                    <i class="fas fa-newspaper"></i> <span>News</span>
                </a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="header">
                <div>
                    <h1><i class="fas fa-building"></i> {{ $admin->department }} Dashboard</h1>
                    <div class="department-badge">
                        <i class="fas fa-user-shield"></i>
                        Department Administrator: {{ $admin->username }}
                    </div>
                    @php
                        $departmentNames = [
                            'BSIT' => 'Bachelor of Science in Information Technology',
                            'BSBA' => 'Bachelor of Science in Business Administration',
                            'BEED' => 'Bachelor of Elementary Education',
                            'BSHM' => 'Bachelor of Science in Hospitality Management',
                            'BSED' => 'Bachelor of Secondary Education',
                        ];
                        $deptFullName = $departmentNames[$admin->department] ?? null;
                    @endphp
                    @if($deptFullName)
                        <div class="department-badge" style="background: linear-gradient(135deg, #3b82f6, #2563eb); margin-top: 0.5rem;">
                            <i class="fas fa-graduation-cap"></i>
                            Program: {{ $deptFullName }}
                        </div>
                    @endif

                </div>
                <button onclick="handleLogout()" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>

            @if(session('success'))
                <div style="background: #10b981; color: white; padding: 1rem; border-radius: 10px; margin-bottom: 1rem;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="background: #ef4444; color: white; padding: 1rem; border-radius: 10px; margin-bottom: 1rem;">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Statistics Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3><i class="fas fa-bullhorn"></i> My Announcements</h3>
                    <div class="number">{{ $counts['announcements'] }}</div>
                    <div class="change">Published content</div>
                </div>
                <div class="stat-card">
                    <h3><i class="fas fa-calendar-alt"></i> My Events</h3>
                    <div class="number">{{ $counts['events'] }}</div>
                    <div class="change">Scheduled events</div>
                </div>
                <div class="stat-card">
                    <h3><i class="fas fa-newspaper"></i> My News</h3>
                    <div class="number">{{ $counts['news'] }}</div>
                    <div class="change">Published articles</div>
                </div>
                <div class="stat-card">
                    <h3><i class="fas fa-users"></i> Department Users</h3>
                    <div class="number">{{ $departmentStats['department_users'] }}</div>
                    <div class="change">{{ $counts['department_students'] }} Students</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="chart-container">
                <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
                <div class="quick-actions-grid">
                    <a href="{{ route('department-admin.announcements.create') }}" class="quick-action-card">
                        <div class="action-icon">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <h4>New Announcement</h4>
                        <p>Create department announcement</p>
                    </a>
                    <a href="{{ route('department-admin.events.create') }}" class="quick-action-card">
                        <div class="action-icon">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <h4>Schedule Event</h4>
                        <p>Add department event</p>
                    </a>
                    <a href="{{ route('department-admin.news.create') }}" class="quick-action-card">
                        <div class="action-icon">
                            <i class="fas fa-newspaper"></i>
                        </div>
                        <h4>Publish News</h4>
                        <p>Share department news</p>
                    </a>
                </div>
            </div>

            <!-- Charts -->
            <div class="chart-container">
                <h2><i class="fas fa-chart-line"></i> My Content Activity (Last 7 Days)</h2>
                <div class="chart-wrapper">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Enhanced Activity Chart with Professional Styling - Department Admin Theme
        const ctx = document.getElementById('activityChart').getContext('2d');

        // Create gradient backgrounds using department admin theme colors (black/gray)
        const announcementsGradient = ctx.createLinearGradient(0, 0, 0, 400);
        announcementsGradient.addColorStop(0, 'rgba(59, 130, 246, 0.3)');
        announcementsGradient.addColorStop(1, 'rgba(59, 130, 246, 0.05)');

        const eventsGradient = ctx.createLinearGradient(0, 0, 0, 400);
        eventsGradient.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
        eventsGradient.addColorStop(1, 'rgba(16, 185, 129, 0.05)');

        const newsGradient = ctx.createLinearGradient(0, 0, 0, 400);
        newsGradient.addColorStop(0, 'rgba(245, 158, 11, 0.3)');
        newsGradient.addColorStop(1, 'rgba(245, 158, 11, 0.05)');

        const activityChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartData['labels']),
                datasets: [
                    {
                        label: '📢 Announcements',
                        data: @json($chartData['announcements']),
                        borderColor: '#3b82f6',
                        backgroundColor: announcementsGradient,
                        borderWidth: 3,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 3,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: '📅 Events',
                        data: @json($chartData['events']),
                        borderColor: '#10b981',
                        backgroundColor: eventsGradient,
                        borderWidth: 3,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 3,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: '📰 News',
                        data: @json($chartData['news']),
                        borderColor: '#f59e0b',
                        backgroundColor: newsGradient,
                        borderWidth: 3,
                        pointBackgroundColor: '#f59e0b',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 3,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                },
                hover: {
                    animationDuration: 1500
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12,
                                weight: '600',
                                family: 'Inter'
                            },
                            color: '#6b7280',
                            generateLabels: function(chart) {
                                const datasets = chart.data.datasets;
                                return datasets.map((dataset, i) => ({
                                    text: dataset.label,
                                    fillStyle: dataset.borderColor,
                                    strokeStyle: dataset.borderColor,
                                    lineWidth: 3,
                                    pointStyle: 'circle',
                                    index: i
                                }));
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.95)',
                        titleColor: '#f9fafb',
                        bodyColor: '#f3f4f6',
                        borderColor: 'rgba(107, 114, 128, 0.3)',
                        borderWidth: 1,
                        cornerRadius: 12,
                        displayColors: true,
                        usePointStyle: true,
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            title: function(tooltipItems) {
                                return tooltipItems[0].label.replace(/[📢📅📰]/g, '').trim();
                            },
                            label: function(context) {
                                return `${context.dataset.label}: ${context.parsed.y} items`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(156, 163, 175, 0.1)',
                            borderDash: [5, 5]
                        },
                        ticks: {
                            color: '#6b7280',
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            padding: 10
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(156, 163, 175, 0.1)',
                            borderDash: [5, 5]
                        },
                        ticks: {
                            color: '#6b7280',
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            padding: 10,
                            callback: function(value) {
                                return value + ' items';
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                elements: {
                    point: {
                        hoverBorderWidth: 4,
                        hoverShadowOffsetX: 0,
                        hoverShadowOffsetY: 4,
                        hoverShadowBlur: 10,
                        hoverShadowColor: 'rgba(0, 0, 0, 0.1)'
                    }
                }
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            activityChart.resize();
        });

        // Mobile sidebar functionality
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        }

        // Auto-close sidebar on window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                closeSidebar();
            }
        });

        // Active menu item highlighting
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const menuLinks = document.querySelectorAll('.sidebar-menu a');
            
            menuLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === currentPath || 
                    (currentPath.includes(link.getAttribute('href')) && link.getAttribute('href') !== '/')) {
                    link.classList.add('active');
                }
            });
        });

        // SweetAlert logout functionality
        async function handleLogout() {
            const result = await Swal.fire({
                title: 'Are you sure?',
                text: 'You will be logged out of your account.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, logout',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            });
            
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Logging out...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Create and submit logout form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('department-admin.logout') }}';
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                form.appendChild(csrfToken);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
