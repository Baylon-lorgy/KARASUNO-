<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title') - Botanical Garden Management</title>

    <!-- Fonts and Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #2E7D32;
            --secondary-color: #43A047;
            --accent-color: #81C784;
            --success-color: #66BB6A;
            --warning-color: #FFA726;
            --danger-color: #EF5350;
            --background-light: #F5F9F6;
            --background-dark: #1C2321;
            --text-primary: #2C3E50;
            --text-secondary: #607D8B;
            --border-radius: 1rem;
            --transition: all 0.3s ease;
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.07);
            --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
            --sidebar-width: 280px;
            
            /* New color variables for gradient */
            --color-sage: #E8F3E9;
            --color-mint: #DCE8E0;
            --color-sky: #E3EDF2;
            --color-lavender: #E9EDF5;
        }

    body {
            font-family: 'Outfit', sans-serif;
            background: 
                linear-gradient(
                    135deg,
                    var(--color-sage) 0%,
                    var(--color-mint) 25%,
                    var(--color-sky) 50%,
                    var(--color-lavender) 100%
                );
            color: var(--text-primary);
      min-height: 100vh;
            transition: var(--transition);
            overflow-x: hidden;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                linear-gradient(
                    rgba(255, 255, 255, 0.02) 1px,
                    transparent 1px
                ),
                linear-gradient(
                    90deg,
                    rgba(255, 255, 255, 0.02) 1px,
                    transparent 1px
                );
            background-size: 20px 20px;
            pointer-events: none;
            z-index: 0;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            left: 1.5rem;
            top: 1.5rem;
            bottom: 1.5rem;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            transition: var(--transition);
            overflow-y: auto;
            color: white;
        }

        .sidebar-header {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-brand {
            font-size: 1.5rem;
            font-weight: 600;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }

        .sidebar-brand img {
            width: 40px;
            height: 40px;
        }

        .nav-item {
            padding: 0.25rem 1rem;
        }

        .nav-link {
      display: flex;
            align-items: center;
            padding: 0.875rem 1.25rem;
            color: rgba(255,255,255,0.8);
            border-radius: 0.75rem;
            transition: var(--transition);
            gap: 1rem;
            font-weight: 500;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }

        .nav-link.active {
            background: white;
            color: var(--primary-color);
            box-shadow: var(--shadow-md);
        }

        .nav-link i {
            font-size: 1.25rem;
        }

        /* Main Content */
    .main-content {
            margin-left: calc(var(--sidebar-width) + 3rem);
            padding: 1.5rem;
            transition: var(--transition);
      min-height: 100vh;
        }

        /* Navbar */
        .top-navbar {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .card-header {
            background: none;
            padding: 1.5rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        /* Buttons */
        .btn {
            border-radius: 0.75rem;
            padding: 0.625rem 1.25rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            box-shadow: var(--shadow-sm);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* Dropdowns */
        .dropdown-menu {
            border: none;
            border-radius: 0.75rem;
            box-shadow: var(--shadow-md);
            padding: 0.5rem;
            min-width: 240px;
        }

        .dropdown-item {
            padding: 0.625rem 1rem;
            border-radius: 0.5rem;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .dropdown-item:hover {
            background: var(--background-light);
            transform: translateX(5px);
        }

        .dropdown-item i {
            font-size: 1.25rem;
            color: var(--primary-color);
        }

        /* User Menu */
        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.5rem;
            border-radius: 0.75rem;
            transition: var(--transition);
            cursor: pointer;
        }

        .user-menu:hover {
            background: var(--background-light);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-color);
        }

        /* Dark Mode */
        body.dark-mode {
            background: var(--background-dark);
            color: white;
        }

        .dark-mode .sidebar {
            background: linear-gradient(135deg, #1B5E20, #2E7D32);
        }

        .dark-mode .top-navbar,
        .dark-mode .card,
        .dark-mode .dropdown-menu {
            background: #2C3E50;
            border-color: rgba(255,255,255,0.1);
        }

        .dark-mode .dropdown-item {
            color: white;
        }

        .dark-mode .dropdown-item:hover {
            background: rgba(255,255,255,0.1);
        }

        /* Responsive Design */
    @media (max-width: 1200px) {
            .sidebar {
                left: -100%;
                margin: 0;
                height: 100vh;
                top: 0;
                border-radius: 0;
            }

            .sidebar.show {
                left: 0;
            }

      .main-content {
        margin-left: 0;
      }
    }

        @media (max-width: 768px) {
            .top-navbar {
                padding: 0.75rem 1rem;
            }

            .card-header {
                padding: 1rem;
            }

            .nav-link {
                padding: 0.75rem 1rem;
            }

            .dropdown-menu {
                min-width: 200px;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.1);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.3s ease forwards;
        }

        /* Content Container */
        .content-container {
            padding: 2rem 1.5rem;
            margin-top: 1rem;
        }

        @media (max-width: 768px) {
            .content-container {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
                <i class="bi bi-flower1"></i>
                <span>Botanical Garden</span>
      </a>
    </div>

        <nav class="mt-4">
            <ul class="nav flex-column">
        <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
                    <a href="{{ route('admin.water.schedule') }}" class="nav-link {{ request()->routeIs('admin.water.schedule') ? 'active' : '' }}">
                        <i class="bi bi-calendar2-check"></i>
                        <span>Water Schedule</span>
            </a>
        </li>
        <li class="nav-item">
                    <a href="{{ route('admin.logs') }}" class="nav-link {{ request()->routeIs('admin.logs') ? 'active' : '' }}">
                        <i class="bi bi-journal-text"></i>
                        <span>System Logs</span>
            </a>
        </li>
        <li class="nav-item">
                    <a href="{{ route('admin.history') }}" class="nav-link {{ request()->routeIs('admin.history') ? 'active' : '' }}">
                        <i class="bi bi-clock-history"></i>
                        <span>History</span>
            </a>
        </li>
    </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navbar -->
        <nav class="top-navbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-link d-xl-none p-0 text-primary" onclick="toggleSidebar()">
                    <i class="bi bi-list" style="font-size: 1.5rem;"></i>
                </button>
                <h4 class="mb-0">@yield('title')</h4>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Notifications -->
                <div class="dropdown">
                    <button class="btn btn-link p-0 position-relative" data-bs-toggle="dropdown">
                        <i class="bi bi-bell" style="font-size: 1.25rem;"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            3
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <h6 class="dropdown-header">Notifications</h6>
                        <a class="dropdown-item" href="#">
                            <i class="bi bi-water"></i>
                            <div>
                                <p class="mb-0">Water level is high</p>
                                <small class="text-muted">5 minutes ago</small>
                </div>
              </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-center" href="#">
                            <small>View all notifications</small>
                        </a>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="dropdown">
                    <button class="btn btn-link p-0" data-bs-toggle="dropdown">
                        <div class="user-menu">
                            <img src="https://scontent.fmnl13-4.fna.fbcdn.net/v/t1.6435-9/189074339_105229265109808_2850753376290745007_n.jpg?_nc_cat=111&ccb=1-7&_nc_sid=6ee11a&_nc_eui2=AeHbbQVXsuLvLp-FPJdb4eesC-23odYk8mkL7beh1iTyac93ZcwZI5hQoDAJ43wAz95AAHO3ODgXARzc_HMn6G8_&_nc_ohc=aCec--l_wFkQ7kNvgFKm9ys&_nc_oc=AdgiqDRI9fWJC2-HbnlP1geKA_ESBWMduCkikBODzubrE4KvLqgUJHxWqUvIYFvKGoudkh_loDikXTKYmJOzJrC1&_nc_zt=23&_nc_ht=scontent.fmnl13-4.fna&_nc_gid=Alh9BXBvMbo4cNn4kyjQLH1&oh=00_AYCrioQyIYzjm8YvpDemQzuexdkMMCy3YsbK26Dvd49SlQ&oe=67EBC284" alt="User" class="user-avatar">
                            <div class="d-none d-md-block">
                                <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                                <small class="text-muted">Administrator</small>
                            </div>
                        </div>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <div class="dropdown-header">
                            <h6 class="mb-0">Welcome!</h6>
                      </div>
                        <a class="dropdown-item" href="#">
                            <i class="bi bi-person-circle"></i>
                            <span>My Profile</span>
                        </a>
                        <a class="dropdown-item" href="#" onclick="toggleDarkMode()">
                            <i class="bi bi-moon-stars"></i>
                            <span>Dark Mode</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
        </div>
      </div>
    </nav>
    
        <!-- Page Content Container -->
        <div class="content-container">
            <div class="fade-in">
        @yield('content')
          </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
        // Toggle Sidebar
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }

        // Toggle Dark Mode
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            const isDarkMode = document.body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDarkMode ? 'enabled' : 'disabled');
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            // Check Dark Mode Preference
            const darkMode = localStorage.getItem('darkMode');
            if (darkMode === 'enabled') {
                document.body.classList.add('dark-mode');
            }

            // Auto-hide alerts
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });

            // Initialize all tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Initialize all popovers
            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
        });

        // Close sidebar on mobile when clicking outside
        document.addEventListener('click', (e) => {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.querySelector('.btn-link.d-xl-none');
            
            if (window.innerWidth < 1200 && 
                sidebar.classList.contains('show') && 
                !sidebar.contains(e.target) && 
                e.target !== toggleBtn) {
                sidebar.classList.remove('show');
            }
        });

        // Smooth scroll to top
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
    }
  </script>
</body>
</html>
