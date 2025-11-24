<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SmartMart Campus - Kepala Minimarket')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <style>
        .sidebar {
            transition: all 0.3s ease;
            z-index: 1000;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0 !important;
            }
        }
        @media (min-width: 769px) {
            .main-content {
                margin-left: 16rem !important;
            }
        }
        .kepala-bg {
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
        }
        .profile-kepala {
            background: linear-gradient(135deg, #3b82f6, #1e40af);
        }
        .btn-kepala-primary {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            border: none;
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .btn-kepala-primary:hover {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(16, 185, 129, 0.4);
        }
        .btn-kepala-secondary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            border: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .btn-kepala-secondary:hover {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            transform: translateY(-1px);
        }
        /* Overlay untuk mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
        .sidebar.open + .sidebar-overlay {
            display: block;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Sidebar -->
    <div class="sidebar fixed inset-y-0 left-0 w-64 kepala-bg text-white shadow-lg">
        <div class="p-6 border-b border-blue-500/30">
            <h1 class="text-xl font-bold">
                <i class="fas fa-store mr-2 text-green-400"></i>
                SmartMart Campus
            </h1>
            <p class="text-sm text-blue-200 mt-1">Kepala Minimarket</p>
        </div>
        
        <nav class="p-4 space-y-2">
            <a href="{{ route('kepala.dashboard') }}" 
               class="flex items-center p-3 text-blue-100 rounded-lg hover:bg-blue-500/30 hover:text-white transition {{ request()->routeIs('kepala.dashboard') ? 'bg-blue-500/30 text-white font-semibold' : '' }}">
                <i class="fas fa-tachometer-alt mr-3"></i>
                Dashboard
            </a>
            
            <a href="{{ route('kepala.laporan-kas') }}" 
               class="flex items-center p-3 text-blue-100 rounded-lg hover:bg-green-500/30 hover:text-white transition {{ request()->routeIs('kepala.laporan-kas') ? 'bg-green-500/30 text-white font-semibold' : '' }}">
                <i class="fas fa-file-invoice-dollar mr-3"></i>
                Laporan Kas
            </a>
            
            <a href="{{ route('kepala.laporan-penjualan') }}" 
               class="flex items-center p-3 text-blue-100 rounded-lg hover:bg-purple-500/30 hover:text-white transition {{ request()->routeIs('kepala.laporan-penjualan') ? 'bg-purple-500/30 text-white font-semibold' : '' }}">
                <i class="fas fa-chart-bar mr-3"></i>
                Laporan Penjualan
            </a>
            
            <a href="{{ route('kepala.laporan-stok') }}" 
               class="flex items-center p-3 text-blue-100 rounded-lg hover:bg-orange-500/30 hover:text-white transition {{ request()->routeIs('kepala.laporan-stok') ? 'bg-orange-500/30 text-white font-semibold' : '' }}">
                <i class="fas fa-boxes mr-3"></i>
                Laporan Stok
            </a>
            
            <!-- Hanya Monitoring Kasir saja -->
            <a href="{{ route('kepala.monitoring.kasir') }}" 
               class="flex items-center p-3 text-blue-100 rounded-lg hover:bg-red-500/30 hover:text-white transition {{ request()->routeIs('kepala.monitoring.kasir') ? 'bg-red-500/30 text-white font-semibold' : '' }}">
                <i class="fas fa-user-check mr-3"></i>
                Monitoring Kasir
            </a>
        </nav>
        
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-blue-500/30">
            <!-- Profile Section -->
            <div class="profile-kepala text-white px-4 py-3 rounded-lg flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-sm">{{ Auth::user()->nama_lengkap ?? 'Kepala Minimarket' }}</p>
                    <p class="text-xs text-blue-200">Role: Kepala</p>
                </div>
            </div>
            
            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition flex items-center justify-center">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Overlay untuk mobile -->
    <div class="sidebar-overlay md:hidden" id="sidebarOverlay"></div>

    <!-- Main Content -->
    <div class="main-content min-h-screen transition-all duration-300">
        <!-- Top Bar -->
        <header class="kepala-bg text-white shadow-lg">
            <div class="flex items-center justify-between p-4">
                <div class="flex items-center">
                    <button id="menuToggle" class="md:hidden p-2 rounded-lg hover:bg-blue-500/30">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h2 class="text-lg font-semibold ml-2 md:ml-0">
                        @yield('title')
                    </h2>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-medium">{{ Auth::user()->nama_lengkap ?? 'Kepala Minimarket' }}</p>
                        <p class="text-xs text-blue-200">{{ now()->translatedFormat('l, d F Y') }}</p>
                    </div>
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-6 bg-gray-100 min-h-screen">
            @yield('content')
        </main>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="fixed top-20 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="fixed top-20 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    <!-- Scripts -->
    <script>
        // Mobile Menu Toggle
        document.getElementById('menuToggle').addEventListener('click', function() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('open');
        });

        // Close sidebar when overlay is clicked
        document.getElementById('sidebarOverlay').addEventListener('click', function() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.remove('open');
        });

        // Auto hide flash messages
        setTimeout(() => {
            const flashMessages = document.querySelectorAll('.fixed');
            flashMessages.forEach(msg => {
                if (msg.classList.contains('bg-green-500') || msg.classList.contains('bg-red-500')) {
                    msg.remove();
                }
            });
        }, 5000);

        // Format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        }

        // Close sidebar when clicking on a link (mobile)
        document.querySelectorAll('.sidebar a').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 768) {
                    document.querySelector('.sidebar').classList.remove('open');
                }
            });
        });
    </script>

    @yield('scripts')
</body>
</html>