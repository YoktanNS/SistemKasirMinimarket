<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SmartMart Campus - Kasir')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS untuk Kasir -->
    <style>
        .kasir-bg {
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
        }
        .btn-kasir-primary {
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
        .btn-kasir-primary:hover {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(16, 185, 129, 0.4);
        }
        .btn-kasir-secondary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            border: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .btn-kasir-secondary:hover {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            transform: translateY(-1px);
        }
        .cart-item {
            transition: all 0.2s ease;
        }
        .cart-item:hover {
            background-color: #f0f9ff;
        }
        .number-input {
            width: 80px;
            text-align: center;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.25rem 0.5rem;
        }
        .profile-kasir {
            background: linear-gradient(135deg, #3b82f6, #1e40af);
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="kasir-bg text-white shadow-lg">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold">
                        <i class="fas fa-cash-register mr-2"></i>SmartMart Campus - Kasir
                    </h1>
                    <!-- Profil Kasir -->
                    <div class="profile-kasir text-white px-4 py-2 rounded-lg flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <div class="font-semibold">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-white/80">Staff Kasir</div>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Navigation -->
                    <nav class="flex space-x-2">
                        <a href="{{ route('kasir.index') }}" 
                           class="px-4 py-2 rounded-lg transition {{ request()->routeIs('kasir.index') ? 'bg-white/20 font-semibold' : 'hover:bg-white/10' }}">
                            <i class="fas fa-shopping-cart mr-2"></i>Transaksi
                        </a>
                        <a href="{{ route('kasir.dashboard') }}" 
                           class="px-4 py-2 rounded-lg transition {{ request()->routeIs('kasir.dashboard') ? 'bg-white/20 font-semibold' : 'hover:bg-white/10' }}">
                            <i class="fas fa-chart-bar mr-2"></i>Dashboard
                        </a>
                        <a href="{{ route('kasir.riwayat') }}" 
                           class="px-4 py-2 rounded-lg transition {{ request()->routeIs('kasir.riwayat') ? 'bg-white/20 font-semibold' : 'hover:bg-white/10' }}">
                            <i class="fas fa-history mr-2"></i>Riwayat
                        </a>
                        <a href="{{ route('kasir.daftar-produk') }}" 
                           class="px-4 py-2 rounded-lg transition {{ request()->routeIs('kasir.daftar-produk') ? 'bg-white/20 font-semibold' : 'hover:bg-white/10' }}">
                            <i class="fas fa-boxes mr-2"></i>Produk
                        </a>
                    </nav>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-6">
        @yield('content')
    </main>

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
        // Auto hide flash messages
        setTimeout(() => {
            const flashMessages = document.querySelectorAll('.fixed');
            flashMessages.forEach(msg => msg.remove());
        }, 5000);

        // Format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        }
    </script>

    @yield('scripts')
</body>
</html>