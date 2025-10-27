<!-- ============================================
FILE: resources/views/layouts/dashboard-layout.blade.php
DESCRIPTION: Shared layout for all dashboards
============================================ -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - iBMIS | Barangay Calbueg</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #2B5CE6;
            --dark-blue: #1E3A8A;
            --gold: #FFA500;
            --light-gray: #F5F7FA;
            --card-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--light-gray);
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 240px;
            background: var(--primary-blue);
            color: white;
            padding: 0;
            position: fixed;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 25px 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            background:  #2B5CE6;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
            width: 100%;
        }

        .logo {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            padding: 5px;
        }

        .logo img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .brand-info {
            flex: 1;
        }

        .brand-name {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1.2;
            color: white;
        }

        .brand-subtitle {
            font-size: 0.9rem;
            opacity: 1;
            line-height: 1.3;
            color: white;
        }

        .user-section {
            background: #2563EB;
            padding: 18px 20px;
            margin: 0;
            border-radius: 0;
        }

        .user-role {
            font-size: 0.8rem;
            color: #FFA500;
            font-weight: 700;
            text-transform: capitalize;
            margin-bottom: 4px;
        }

        .user-name {
            font-weight: 600;
            color: white;
            font-size: 1rem;
        }

        .nav-menu {
            margin-top: 5%;
            list-style: none;
            flex: 1;
            overflow-y: auto;
            padding: 0;
            margin: ;
        }

        .nav-menu::-webkit-scrollbar {
            width: 5px;
        }

        .nav-menu::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.05);
        }

        .nav-menu::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
        }

        .nav-menu::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.3);
        }

        .nav-item {
            margin: 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            color: white;
            text-decoration: none;
            transition: all 0.2s;
            font-size: 0.95rem;
            border-left: 3px solid transparent;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.08);
            color: white;
            border-left-color: var(--gold);
        }

        .nav-link.active {
            background: var(--gold);
            color: var(--dark-blue);
            font-weight: 600;
            border-left-color: var(--gold);
        }

        .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        .main-content {
            margin-left: 240px;
            flex: 1;
            padding: 30px;
        }

        .header-section {
            background: var(--primary-blue);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            position: relative;
        }

        .header-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .header-subtitle {
            opacity: 0.9;
            margin-bottom: 12px;
        }

        .date-badge {
            position: absolute;
            top: 30px;
            right: 30px;
            background: white;
            color: var(--primary-blue);
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .stat-info h3 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .stat-info p {
            color: #666;
            margin: 0;
        }

        .stat-trend {
            font-size: 0.85rem;
            color: #10B981;
            display: flex;
            align-items: center;
            gap: 4px;
            margin-top: 4px;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
        }

        .icon-blue { background: #4F9CF9; }
        .icon-orange { background: #FF8C42; }
        .icon-green { background: #10B981; }
        .icon-purple { background: #A855F7; }
        .icon-pink { background: #EC4899; }

        .activities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
        }

        .activity-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
        }

        .activity-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #E5E7EB;
        }

        .activity-header.blue {
            color: var(--primary-blue);
        }

        .activity-header.orange {
            color: #FF8C42;
        }

        .activity-header h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0;
        }

        .activity-item {
            display: flex;
            gap: 12px;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: background 0.3s;
        }

        .activity-item:hover {
            background: #F9FAFB;
        }

        .activity-icon {
            width: 10px;
            height: 10px;
            background: #FF8C42;
            border-radius: 50%;
            margin-top: 6px;
            flex-shrink: 0;
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 4px;
        }

        .activity-meta {
            font-size: 0.85rem;
            color: #6B7280;
        }

        .event-item {
            display: flex;
            gap: 15px;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 12px;
            background: #F9FAFB;
        }

        .event-date {
            background: var(--primary-blue);
            color: white;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            min-width: 60px;
        }

        .event-day {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
        }

        .event-month {
            font-size: 0.75rem;
            opacity: 0.9;
        }

        .event-details {
            flex: 1;
        }

        .event-title {
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 4px;
        }

        .event-time {
            font-size: 0.85rem;
            color: #6B7280;
        }

        .btn {
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: var(--primary-blue);
            color: white;
        }

        .btn-primary:hover {
            background: var(--dark-blue);
            color: white;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }
            
            .main-content {
                margin-left: 200px;
                padding: 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .activities-grid {
                grid-template-columns: 1fr;
            }

            .date-badge {
                position: static;
                display: inline-block;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo-section">
                <div class="logo">
                    <i class="fas fa-shield-alt" style="color: var(--primary-blue); font-size: 1.5rem;"></i>
                </div>
                <div class="brand-info">
                    <div class="brand-name">iBMIS</div>
                    <div class="brand-subtitle">Barangay Calbueg</div>
                </div>
            </div>
        </div>

        <div class="user-section">
            <div class="user-role">
                @if($user->role === 'barangay_captain')
                    Barangay Captain
                @elseif($user->role === 'secretary')
                    Barangay Secretary
                @elseif($user->role === 'treasurer')
                    Barangay Treasurer
                @elseif($user->role === 'kagawad')
                    Barangay Kagawad
                @else
                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                @endif
            </div>
            <div class="user-name">{{ $user->first_name }} {{ $user->last_name }}</div>
        </div>

        <ul class="nav-menu">
            @yield('nav-items')
            
            <li class="nav-item" style="margin-top: auto;">
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="nav-link" style="width: 100%; background: none; border: none; text-align: left; cursor: pointer;">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>