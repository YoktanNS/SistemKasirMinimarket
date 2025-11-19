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
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <div class="sidebar fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg">
        <div class="p-6 border-b border-gray-200">
            <h1 class="text-xl font-bold text-gray-800">
                <i class="fas fa-store mr-2 text-green-500"></i>
                SmartMart Campus
            </h1>
            <p class="text-sm text-gray-600 mt-1">Kepala Minimarket</p>
        </div>
        
        <nav class="p-4 space-y-2">
            <a href="{{ route('kepala.dashboard') }}" 
               class="flex items-center p-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition {{ request()->routeIs('kepala.dashboard') ? 'bg-blue-50 text-blue-700' : '' }}">
                <i class="fas fa-tachometer-alt mr-3"></i>
                Dashboard
            </a>
            
            <a href="{{ route('kepala.laporan.kas') }}" 
               class="flex items-center p-3 text-gray-700 rounded-lg hover:bg-green-50 hover:text-green-700 transition {{ request()->routeIs('kepala.laporan.*') ? 'bg-green-50 text-green-700' : '' }}">
                <i class="fas fa-file-invoice-dollar mr-3"></i>
                Laporan Kas
            </a>
            
            <a href="{{ route('kepala.laporan.penjualan') }}" 
               class="flex items-center p-3 text-gray-700 rounded-lg hover:bg-purple-50 hover:text-purple-700 transition">
                <i class="fas fa-chart-bar mr-3"></i>
                Laporan Penjualan
            </a>
            
            <a href="#" 
               class="flex items-center p-3 text-gray-700 rounded-lg hover:bg-orange-50 hover:text-orange-700 transition">
                <i class="fas fa-boxes mr-3"></i>
                Manajemen Stok
            </a>
            
            <a href="#" 
               class="flex items-center p-3 text-gray-700 rounded-lg hover:bg-red-50 hover:text-red-700 transition">
                <i class="fas fa-users mr-3"></i>
                Manajemen Kasir
            </a>
        </nav>
        
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-shield text-blue-600"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-800 text-sm">{{ Auth::user()->nama_lengkap ?? 'Kepala Minimarket' }}</p>
                    <p class="text-xs text-gray-600">Role: Kepala</p>
                </div>
            </div>
            
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="w-full flex items-center p-2 text-gray-700 rounded-lg hover:bg-gray-100 transition">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="ml-0 md:ml-64 min-h-screen">
        <!-- Top Bar -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between p-4">
                <div class="flex items-center">
                    <button id="menuToggle" class="md:hidden p-2 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-bars text-gray-600"></i>
                    </button>
                    <h2 class="text-lg font-semibold text-gray-800 ml-2 md:ml-0">
                        @yield('title')
                    </h2>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-medium text-gray-800">{{ Auth::user()->nama_lengkap ?? 'Kepala Minimarket' }}</p>
                        <p class="text-xs text-gray-600">{{ now()->translatedFormat('l, d F Y') }}</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-shield text-blue-600"></i>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-6">
            @yield('content')
        </main>
    </div>

    <!-- Mobile Menu Toggle Script -->
    <script>
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('open');
        });
    </script>
</body>
</html>