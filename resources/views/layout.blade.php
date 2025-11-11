<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Informasi Minimarket')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full">
    
    <div class="min-h-full">
        <nav class="bg-blue-600 shadow-md">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-2xl font-bold text-white">Sistem Informasi Minimarket Kampus</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('tps') }}" 
                           class="{{ Request::routeIs('tps') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-500 hover:text-white' }} rounded-md px-3 py-2 text-sm font-medium">
                           TPS - Kasir
                        </a>
                        <a href="{{ route('dashboard') }}" 
                           class="{{ Request::routeIs('dashboard') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-500 hover:text-white' }} rounded-md px-3 py-2 text-sm font-medium">
                           MIS - Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <header class="bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                <h1 class="text-xl font-semibold tracking-tight text-gray-900">
                    @yield('header')
                </h1>
            </div>
        </header>

        <main>
            <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>