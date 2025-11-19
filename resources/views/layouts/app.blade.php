<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | SmartMart Campus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/3081/3081559.png" type="image/png">

    <!-- Custom CSS untuk konsistensi warna dengan Kasir -->
    <style>
        .admin-bg {
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
        }

        .btn-admin-primary {
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

        .btn-admin-primary:hover {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(16, 185, 129, 0.4);
        }

        .btn-admin-secondary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            border: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-admin-secondary:hover {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            transform: translateY(-1px);
        }

        .profile-admin {
            background: linear-gradient(135deg, #3b82f6, #1e40af);
        }

        .sidebar-active {
            background-color: rgba(59, 130, 246, 0.1);
            color: #1e40af;
            font-weight: 600;
        }

        .sidebar-hover:hover {
            background-color: rgba(59, 130, 246, 0.05);
        }
    </style>
</head>

<body class="bg-gray-100 font-sans text-gray-800">

    <!-- Navbar - SAMA DENGAN KASIR -->
    <nav class="admin-bg text-white shadow-lg">
        <div class="max-w-7xl mx-auto flex items-center justify-between px-10 py-4">

            <!-- Kiri: Logo + Nama - SAMA DENGAN KASIR -->
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 group">
                <img src="https://cdn-icons-png.flaticon.com/512/3081/3081559.png"
                    alt="Logo"
                    class="w-11 h-11 transform group-hover:scale-110 transition duration-200">
                <h1 class="text-2xl font-extrabold tracking-wide group-hover:text-gray-100">
                    SmartMart <span class="text-blue-200">Campus</span>
                </h1>
            </a>

            <!-- Kanan: Info User + Logout - SAMA DENGAN KASIR -->
            <div class="flex items-center space-x-6">
                <div class="profile-admin px-4 py-1.5 rounded-full shadow-sm border border-blue-400">
                    <span class="text-sm font-semibold">
                        👤 {{ Auth::user()->nama_lengkap ?? 'Admin' }}
                    </span>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-semibold px-5 py-2 rounded-lg transition-all shadow-md">
                        <i class="fas fa-sign-out-alt mr-2"></i>Keluar
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Layout Utama -->
    <div class="flex">
        <!-- Sidebar - WARNA DISESUAIKAN -->
        <aside class="w-64 bg-white shadow-xl min-h-screen p-6 space-y-3 border-r border-gray-200">
            <h2 class="text-gray-600 uppercase text-sm font-semibold mb-4">Navigasi Admin</h2>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                        class="block px-4 py-2 rounded-md transition sidebar-hover {{ request()->routeIs('admin.dashboard') ? 'sidebar-active' : '' }}">
                        <i class="fas fa-chart-line mr-2"></i>Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.produk.index') }}"
                        class="block px-4 py-2 rounded-md transition sidebar-hover {{ request()->routeIs('admin.produk.*') ? 'sidebar-active' : '' }}">
                        <i class="fas fa-boxes mr-2"></i>Data Produk
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.kategori.index') }}"
                        class="block px-4 py-2 rounded-md transition sidebar-hover {{ request()->routeIs('admin.kategori.*') ? 'sidebar-active' : '' }}">
                        <i class="fas fa-tags mr-2"></i>Kategori Produk
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.stok.index') }}"
                        class="block px-4 py-2 rounded-md transition sidebar-hover {{ request()->routeIs('admin.stok.*') ? 'sidebar-active' : '' }}">
                        <i class="fas fa-chart-bar mr-2"></i>Stok Barang
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.supplier.index') }}"
                        class="block px-4 py-2 rounded-md transition sidebar-hover {{ request()->routeIs('admin.supplier.*') ? 'sidebar-active' : '' }}">
                        <i class="fas fa-truck mr-2"></i>Supplier
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.arsip.index') }}"
                        class="block px-4 py-2 rounded-md transition sidebar-hover {{ request()->routeIs('admin.arsip.*') ? 'sidebar-active' : '' }}">
                        <i class="fas fa-archive mr-2"></i>Arsip Dokumen
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.laporan.index') }}"
                        class="block px-4 py-2 rounded-md transition sidebar-hover {{ request()->routeIs('admin.laporan.*') ? 'sidebar-active' : '' }}">
                        <i class="fas fa-file-alt mr-2"></i>Laporan
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Konten -->
        <main class="flex-1 p-10">
            <h2 class="text-3xl font-bold mb-6 text-blue-800">
                @yield('page_title')
            </h2>
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Flash Messages - SAMA DENGAN KASIR -->
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

    <!-- Font Awesome untuk icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    <script>
        // Auto hide flash messages - SAMA DENGAN KASIR
        setTimeout(() => {
            const flashMessages = document.querySelectorAll('.fixed');
            flashMessages.forEach(msg => msg.remove());
        }, 5000);

        // Format currency - SAMA DENGAN KASIR
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