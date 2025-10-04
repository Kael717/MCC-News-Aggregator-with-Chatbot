<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - {{ $office }} Office</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 260px;
            --header-height: 80px;
            --transition-speed: 0.3s;
            --border-radius: 12px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            --card-padding: 1.5rem;

            /* Color variables based on office */
            @if($office === 'NSTP')
                --primary-color: #10b981;
                --primary-light: #d1fae5;
                --primary-dark: #059669;
                --primary-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
            @elseif($office === 'SSC')
                --primary-color: #3b82f6;
                --primary-light: #dbeafe;
                --primary-dark: #2563eb;
                --primary-gradient: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            @elseif($office === 'GUIDANCE')
                --primary-color: #8b5cf6;
                --primary-light: #ede9fe;
                --primary-dark: #7c3aed;
                --primary-gradient: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            @elseif($office === 'REGISTRAR')
                --primary-color: #f59e0b;
                --primary-light: #fef3c7;
                --primary-dark: #d97706;
                --primary-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            @elseif($office === 'CLINIC')
                --primary-color: #ef4444;
                --primary-light: #fee2e2;
                --primary-dark: #dc2626;
                --primary-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            @else
                --primary-color: #667eea;
                --primary-light: #e0e7ff;
                --primary-dark: #5b67d7;
                --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            @endif

            --secondary-color: #06b6d4;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #3b82f6;

            --text-primary: #111827;
            --text-secondary: #4b5563;
            --text-muted: #6b7280;
            --text-light: #9ca3af;

            --bg-primary: #ffffff;
            --bg-secondary: #f9fafb;
            --bg-sidebar: #1f2937;
            --bg-sidebar-hover: rgba(255, 255, 255, 0.08);

            --border-color: #e5e7eb;
            --border-light: #f3f4f6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-secondary);
            color: var(--text-primary);
            line-height: 1.5;
            min-height: 100vh;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles - Enhanced to match department admin */
        .sidebar {
            width: var(--sidebar-width);
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
            display: flex;
            flex-direction: column;
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
            margin-bottom: 0.5rem;
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
            color: white;
            font-size: 1.4rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #ffffff 0%, #e5e7eb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
        }

        .sidebar-header h3 i {
            font-size: 1.6rem;
            background: linear-gradient(135deg, #ffffff 0%, #d1d5db 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }

        .office-info {
            color: var(--text-light);
            font-size: 0.8rem;
            margin-top: 0.5rem;
            font-weight: 400;
            opacity: 0.8;
        }

        .sidebar-menu {
            list-style: none;
            padding: 1rem 0;
            flex-grow: 1;
        }

        .sidebar-menu li {
            margin: 0.25rem 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.25rem;
            color: #d1d5db;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            gap: 0.75rem;
            font-size: 0.9rem;
            border-left: 3px solid transparent;
            margin: 0 0.5rem;
            border-radius: 6px;
            position: relative;
            overflow: hidden;
        }

        .sidebar-menu a:hover {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.05) 100%);
            color: white;
            border-left-color: var(--primary-color);
            transform: translateX(4px);
        }

        .sidebar-menu a.active {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.05) 100%);
            color: white;
            border-left-color: var(--primary-color);
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .sidebar-menu a i {
            width: 20px;
            text-align: center;
            font-size: 0.95rem;
        }

        .logout-btn {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            width: calc(100% - 1rem);
            text-align: left;
            padding: 0.75rem 1.25rem;
            cursor: pointer;
            transition: all var(--transition-speed) ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.9rem;
            margin: 0.5rem;
            border-radius: 6px;
        }

        .logout-btn:hover {
            background: var(--bg-sidebar-hover);
            color: white;
        }

        .logout-btn i {
            width: 20px;
            text-align: center;
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-footer .logout-btn {
            width: 100%;
            text-align: left;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* Main Content Styles */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 1.5rem;
            width: calc(100% - var(--sidebar-width));
            transition: margin-left var(--transition-speed) ease;
        }

        .header {
            background: var(--bg-primary);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .header h1 i {
            color: var(--primary-color);
            font-size: 1.8rem;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Table and Content Styles */
        .content-container {
            background: var(--bg-primary);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background: var(--bg-secondary);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-primary);
            border-bottom: 1px solid var(--border-color);
        }

        .table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-light);
            vertical-align: middle;
        }

        .table tr:hover {
            background: var(--bg-secondary);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-published {
            background: var(--primary-light);
            color: var(--primary-dark);
        }

        .status-draft {
            background: #fef3c7;
            color: #92400e;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--text-light);
            margin-bottom: 1rem;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 1rem;
            }

            .header h1 {
                font-size: 1.5rem;
            }

            .table {
                font-size: 0.875rem;
            }

            .table th,
            .table td {
                padding: 0.75rem 0.5rem;
            }

            .actions {
                flex-direction: column;
            }
        }

        /* Mobile menu button */
        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1001;
            background: var(--bg-sidebar);
            color: white;
            border: none;
            padding: 0.65rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .mobile-menu-btn:hover {
            background: var(--primary-dark);
            transform: scale(1.05);
        }

        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }
        }

        /* Enhanced Typography */
        h1, h2, h3, h4, h5, h6 {
            font-weight: 600;
            line-height: 1.25;
        }

        /* Smooth transitions */
        a, button, .btn {
            transition: all 0.2s ease-out;
        }

        .header h1 i {
            @if($office === 'NSTP')
                color: #10b981;
            @elseif($office === 'SSC')
                color: #3b82f6;
            @elseif($office === 'GUIDANCE')
                color: #8b5cf6;
            @elseif($office === 'REGISTRAR')
                color: #f59e0b;
            @elseif($office === 'CLINIC')
                color: #ef4444;
            @else
                color: #667eea;
            @endif
            font-size: 2.2rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            @if($office === 'NSTP')
                background: linear-gradient(135deg, #10b981, #059669);
            @elseif($office === 'SSC')
                background: linear-gradient(135deg, #3b82f6, #2563eb);
            @elseif($office === 'GUIDANCE')
                background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            @elseif($office === 'REGISTRAR')
                background: linear-gradient(135deg, #f59e0b, #d97706);
            @elseif($office === 'CLINIC')
                background: linear-gradient(135deg, #ef4444, #dc2626);
            @else
                background: linear-gradient(135deg, #667eea, #764ba2);
            @endif
            color: white;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .btn-info {
            background: var(--info-color);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .content-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .content-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content-header h2 {
            color: #1f2937;
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 1rem 2rem;
            text-align: left;
            border-bottom: 1px solid #f3f4f6;
        }

        .table th {
            background: #f9fafb;
            color: #374151;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }

        .table tbody tr:hover {
            background: #f9fafb;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-published {
            background: #dcfce7;
            color: #166534;
        }

        .status-draft {
            background: #fef3c7;
            color: #92400e;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #d1d5db;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: #374151;
        }

        .empty-state p {
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .table {
                font-size: 0.875rem;
            }

            .table th,
            .table td {
                padding: 0.75rem 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Mobile Menu Button -->
        <button class="mobile-menu-btn" onclick="toggleSidebar()" aria-label="Toggle menu">
            <i class="fas fa-bars"></i>
        </button>

        <div class="sidebar">
            <div class="sidebar-header">
                <h3>
                    @if($office === 'NSTP')
                        <i class="fas fa-flag"></i>
                    @elseif($office === 'SSC')
                        <i class="fas fa-users"></i>
                    @elseif($office === 'GUIDANCE')
                        <i class="fas fa-heart"></i>
                    @elseif($office === 'REGISTRAR')
                        <i class="fas fa-file-alt"></i>
                    @elseif($office === 'CLINIC')
                        <i class="fas fa-stethoscope"></i>
                    @else
                        <i class="fas fa-briefcase"></i>
                    @endif
                    {{ $office }} Office
                </h3>
                <div class="office-info">{{ auth('admin')->user()->username }}</div>
            </div>
            <ul class="sidebar-menu">
                <li><a href="{{ route('office-admin.dashboard') }}">
                    <i class="fas fa-chart-pie"></i> Dashboard
                </a></li>
                <li><a href="{{ route('office-admin.announcements.index') }}">
                    <i class="fas fa-bullhorn"></i> Announcements
                </a></li>
                <li><a href="{{ route('office-admin.events.index') }}" class="active">
                    <i class="fas fa-calendar-alt"></i> Events
                </a></li>
                <li><a href="{{ route('office-admin.news.index') }}">
                    <i class="fas fa-newspaper"></i> News
                </a></li>
                <li>
                   
                </li>
            </ul>

            
        </div>

        <div class="main-content">
            <div class="header">
                <div>
                    <h1><i class="fas fa-calendar-alt"></i> Events</h1>
                </div>
                <div class="header-actions">
                    <a href="{{ route('office-admin.events.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Event
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div style="background: #dcfce7; color: #166534; padding: 1rem 1.5rem; border-radius: 10px; margin-bottom: 1.5rem; border: 1px solid #bbf7d0;">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="background: #fef2f2; color: #dc2626; padding: 1rem 1.5rem; border-radius: 10px; margin-bottom: 1.5rem; border: 1px solid #fecaca;">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            <div class="content-container">
                <div class="content-header">
                    <h2>My Events</h2>
                    <span style="color: #6b7280; font-size: 0.875rem;">{{ $events->count() }} total</span>
                </div>

                @if($events->count() > 0)
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                                <tr>
                                    <td>
                                        <div style="font-weight: 600; color: #1f2937; margin-bottom: 0.25rem;">
                                            {{ $event->title }}
                                        </div>
                                        <div style="color: #6b7280; font-size: 0.875rem;">
                                            {{ Str::limit($event->description, 60) }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge {{ $event->is_published ? 'status-published' : 'status-draft' }}">
                                            {{ $event->is_published ? 'Published' : 'Draft' }}
                                        </span>
                                    </td>
                                    <td style="color: #6b7280;">
                                        {{ $event->created_at->format('M d, Y') }}
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="{{ route('office-admin.events.show', $event) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('office-admin.events.edit', $event) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('office-admin.events.destroy', $event) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this event?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    
                @endif
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('active');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.sidebar');
            const mobileBtn = document.querySelector('.mobile-menu-btn');

            if (window.innerWidth <= 768 &&
                !sidebar.contains(event.target) &&
                !mobileBtn.contains(event.target) &&
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });
    </script>
</body>
</html>
