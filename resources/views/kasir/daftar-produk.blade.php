@extends('layouts.kasir')

@section('title', 'Daftar Produk - SmartMart Campus')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-boxes mr-2"></i>Daftar Produk
            </h1>
            <p class="text-gray-600 text-sm mt-1">
                Kelola dan tambahkan produk ke keranjang belanja
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('kasir.index') }}" class="btn-kasir-primary flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Kasir
            </a>
            <button onclick="refreshPage()" class="btn-kasir-secondary flex items-center">
                <i class="fas fa-refresh mr-2"></i>Refresh
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 hover:shadow-xl transition cursor-pointer" onclick="filterByStatus('')">
            <div class="flex items-center">
                <div class="bg-green-100 p-3 rounded-full mr-4">
                    <i class="fas fa-box text-green-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Produk</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $totalProduk }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 hover:shadow-xl transition cursor-pointer" onclick="filterByStatus('Tersedia')">
            <div class="flex items-center">
                <div class="bg-blue-100 p-3 rounded-full mr-4">
                    <i class="fas fa-check-circle text-blue-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Stok Tersedia</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $totalStokTersedia }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 hover:shadow-xl transition cursor-pointer" onclick="filterByStatus('Menipis')">
            <div class="flex items-center">
                <div class="bg-yellow-100 p-3 rounded-full mr-4">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Stok Menipis</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $stokMenipis }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 hover:shadow-xl transition cursor-pointer" onclick="filterByStatus('Habis')">
            <div class="flex items-center">
                <div class="bg-red-100 p-3 rounded-full mr-4">
                    <i class="fas fa-times-circle text-red-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Stok Habis</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $stokHabis }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6">
        <form method="GET" action="{{ route('kasir.daftar-produk') }}" id="filterForm" class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-search mr-1"></i>Cari Produk
                </label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Nama produk atau barcode..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    id="searchInput">
            </div>

            <!-- Kategori -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-tag mr-1"></i>Kategori
                </label>
                <select name="kategori_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kategori)
                    <option value="{{ $kategori->kategori_id }}" {{ request('kategori_id') == $kategori->kategori_id ? 'selected' : '' }}>
                        {{ $kategori->nama_kategori }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Stok -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-filter mr-1"></i>Status Stok
                </label>
                <select name="status_stok" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="tersedia" {{ request('status_stok') == 'tersedia' ? 'selected' : '' }}>Stok Tersedia</option>
                    <option value="menipis" {{ request('status_stok') == 'menipis' ? 'selected' : '' }}>Stok Menipis</option>
                    <option value="habis" {{ request('status_stok') == 'habis' ? 'selected' : '' }}>Stok Habis</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="btn-kasir-primary flex-1 flex items-center justify-center">
                    <i class="fas fa-filter mr-2"></i>Terapkan Filter
                </button>
                <button type="button" onclick="clearFilters()" class="bg-gray-500 hover:bg-gray-600 text-white p-2 rounded-lg transition flex items-center justify-center">
                    <i class="fas fa-refresh"></i>
                </button>
            </div>
        </form>

        <!-- Active Filters -->
        @if(request()->anyFilled(['search', 'kategori_id', 'status_stok']))
        <div class="mt-4 flex flex-wrap gap-2">
            <span class="text-sm text-gray-600">Filter aktif:</span>
            @if(request('search'))
            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs flex items-center">
                Pencarian: "{{ request('search') }}"
                <button onclick="removeFilter('search')" class="ml-1 text-blue-600 hover:text-blue-800">
                    <i class="fas fa-times"></i>
                </button>
            </span>
            @endif
            @if(request('kategori_id'))
            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs flex items-center">
                Kategori: {{ $kategoris->where('kategori_id', request('kategori_id'))->first()->nama_kategori ?? 'Unknown' }}
                <button onclick="removeFilter('kategori_id')" class="ml-1 text-green-600 hover:text-green-800">
                    <i class="fas fa-times"></i>
                </button>
            </span>
            @endif
            @if(request('status_stok'))
            <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-xs flex items-center">
                Status: {{ ucfirst(request('status_stok')) }}
                <button onclick="removeFilter('status_stok')" class="ml-1 text-purple-600 hover:text-purple-800">
                    <i class="fas fa-times"></i>
                </button>
            </span>
            @endif
        </div>
        @endif
    </div>

    <!-- Results Info -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
        <div class="text-sm text-gray-600">
            Menampilkan <span class="font-semibold">{{ $produk->count() }}</span> dari 
            <span class="font-semibold">{{ $produk->total() }}</span> produk
            @if(request()->anyFilled(['search', 'kategori_id', 'status_stok']))
            <span class="text-blue-600">(difilter)</span>
            @endif
        </div>
        
        <!-- Sort Options -->
        <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-600">Urutkan:</span>
            <select onchange="sortProducts(this.value)" class="border border-gray-300 rounded-lg px-3 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                <option value="nama_asc" {{ request('sort') == 'nama_asc' ? 'selected' : '' }}>Nama A-Z</option>
                <option value="nama_desc" {{ request('sort') == 'nama_desc' ? 'selected' : '' }}>Nama Z-A</option>
                <option value="harga_asc" {{ request('sort') == 'harga_asc' ? 'selected' : '' }}>Harga Terendah</option>
                <option value="harga_desc" {{ request('sort') == 'harga_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                <option value="stok_asc" {{ request('sort') == 'stok_asc' ? 'selected' : '' }}>Stok Terendah</option>
                <option value="stok_desc" {{ request('sort') == 'stok_desc' ? 'selected' : '' }}>Stok Tertinggi</option>
            </select>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        @if($produk->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6 p-4 sm:p-6">
            @foreach($produk as $item)
            <div class="border border-gray-200 rounded-xl p-4 hover:shadow-lg transition-all duration-300 hover:border-blue-200 product-card"
                 data-product-id="{{ $item->produk_id }}"
                 data-stock="{{ $item->stok_tersedia }}"
                 data-status="{{ $item->status }}">
                
                <!-- Product Image -->
                <div class="text-center mb-3 relative">
                    @php
                        $gambarPath = $item->gambar_produk;
                        $gambarUrl = $gambarPath ? asset('storage/' . $gambarPath) : null;
                        $gambarExists = $gambarPath && file_exists(public_path('storage/' . $gambarPath));
                    @endphp
                    
                    @if($gambarExists)
                    <img src="{{ $gambarUrl }}"
                        alt="{{ $item->nama_produk }}"
                        class="w-20 h-20 object-cover rounded-lg mx-auto border-2 border-gray-200 hover:border-blue-300 transition"
                        loading="lazy"
                        onerror="handleImageError(this, '{{ $item->produk_id }}')">
                    @else
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg flex flex-col items-center justify-center mx-auto border-2 border-blue-200">
                        <i class="fas fa-cube text-blue-400 text-lg mb-1"></i>
                        <span class="text-xs text-blue-600 font-medium text-center px-1 leading-tight">
                            {{ \Illuminate\Support\Str::limit($item->nama_produk, 10) }}
                        </span>
                        @if($gambarPath && !$gambarExists)
                        <span class="text-xs text-red-500 mt-1" title="Gambar tidak ditemukan">⚠️</span>
                        @endif
                    </div>
                    @endif

                    <!-- Stock Badge -->
                    <div class="absolute -top-1 -right-1">
                        @if($item->stok_tersedia > $item->stok_minimum)
                        <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs font-bold shadow-sm">
                            {{ $item->stok_tersedia }}
                        </span>
                        @elseif($item->stok_tersedia > 0)
                        <span class="bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-bold shadow-sm">
                            {{ $item->stok_tersedia }}
                        </span>
                        @else
                        <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs font-bold shadow-sm">
                            <i class="fas fa-times"></i>
                        </span>
                        @endif
                    </div>
                </div>

                <!-- Product Info -->
                <div class="text-center mb-3 space-y-1">
                    <h3 class="font-semibold text-gray-800 text-sm leading-tight" title="{{ $item->nama_produk }}">
                        {{ \Illuminate\Support\Str::limit($item->nama_produk, 40) }}
                    </h3>
                    <p class="text-xs text-gray-500">
                        <i class="fas fa-barcode mr-1"></i>{{ $item->barcode ?: 'No barcode' }}
                    </p>
                    <p class="text-xs text-gray-500">
                        <i class="fas fa-tag mr-1"></i>{{ $item->kategori->nama_kategori ?? 'Tidak ada kategori' }}
                    </p>
                    <p class="text-lg font-bold text-green-600 mt-2">
                        Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                    </p>
                </div>

                <!-- Quick Actions -->
                <div class="flex space-x-2">
                    <button onclick="quickAddToCart('{{ $item->produk_id }}', 1)"
                        class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm transition flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                        {{ $item->stok_tersedia == 0 ? 'disabled' : '' }}
                        id="add-btn-{{ $item->produk_id }}">
                        <i class="fas fa-cart-plus mr-1"></i>
                        <span>Tambah</span>
                    </button>
                    
                    <button onclick="showProductDetail('{{ $item->produk_id }}')"
                        class="bg-gray-500 hover:bg-gray-600 text-white p-2 rounded-lg transition flex items-center justify-center"
                        title="Detail Produk">
                        <i class="fas fa-info"></i>
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($produk->hasPages())
        <div class="bg-gray-50 px-4 sm:px-6 py-4 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-sm text-gray-600">
                    Menampilkan {{ $produk->firstItem() }} - {{ $produk->lastItem() }} dari {{ $produk->total() }} produk
                </div>
                <div class="flex justify-center">
                    {{ $produk->links() }}
                </div>
            </div>
        </div>
        @endif

        @else
        <div class="text-center py-12 text-gray-500">
            <i class="fas fa-box-open text-4xl mb-3"></i>
            <p class="text-lg font-semibold">Tidak ada produk ditemukan</p>
            <p class="text-sm mt-1 mb-4">Coba ubah filter pencarian Anda</p>
            <button onclick="clearFilters()" class="btn-kasir-primary">
                <i class="fas fa-refresh mr-2"></i>Reset Filter
            </button>
        </div>
        @endif
    </div>
</div>

<!-- Notification Container -->
<div id="notificationContainer" class="fixed top-4 right-4 z-50 space-y-2 max-w-sm"></div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden items-center justify-center">
    <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div>
        <span class="text-gray-700">Memproses...</span>
    </div>
</div>

<script>
    // Enhanced quick add to cart
    async function quickAddToCart(produkId, qty = 1) {
        const button = document.getElementById(`add-btn-${produkId}`);
        const originalContent = button.innerHTML;
        
        // Show loading state
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i><span>Loading...</span>';
        button.disabled = true;
        
        try {
            const response = await fetch('{{ route("kasir.quick-add-from-list") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    produk_id: produkId,
                    qty: qty
                })
            });

            const result = await response.json();

            if (result.success) {
                showNotification(result.message, 'success');
                updateCartCounter(result.cart_count);
                
                // Update product stock display
                updateProductStock(produkId, result.updated_stock);
            } else {
                showNotification(result.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Error: Gagal menambahkan produk ke keranjang', 'error');
        } finally {
            // Restore button state
            button.innerHTML = originalContent;
            button.disabled = false;
        }
    }

    // Update product stock display
    function updateProductStock(produkId, newStock) {
        const productCard = document.querySelector(`[data-product-id="${produkId}"]`);
        if (!productCard) return;
        
        const stockBadge = productCard.querySelector('.absolute .bg-green-500, .absolute .bg-yellow-500, .absolute .bg-red-500');
        const addButton = document.getElementById(`add-btn-${produkId}`);
        
        if (stockBadge) {
            if (newStock > 0) {
                stockBadge.className = newStock > 5 ? 
                    'bg-green-500 text-white px-2 py-1 rounded-full text-xs font-bold shadow-sm' :
                    'bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-bold shadow-sm';
                stockBadge.textContent = newStock;
                addButton.disabled = false;
            } else {
                stockBadge.className = 'bg-red-500 text-white px-2 py-1 rounded-full text-xs font-bold shadow-sm';
                stockBadge.innerHTML = '<i class="fas fa-times"></i>';
                addButton.disabled = true;
            }
        }
    }

    // Enhanced notification system
    function showNotification(message, type = 'success', duration = 4000) {
        const container = document.getElementById('notificationContainer');
        const notification = document.createElement('div');
        
        const icons = {
            success: 'check-circle',
            error: 'exclamation-triangle',
            warning: 'exclamation-circle',
            info: 'info-circle'
        };
        
        const colors = {
            success: 'bg-green-500 border-green-600',
            error: 'bg-red-500 border-red-600',
            warning: 'bg-yellow-500 border-yellow-600',
            info: 'bg-blue-500 border-blue-600'
        };
        
        notification.className = `${colors[type]} text-white p-4 rounded-lg shadow-xl transform transition-all duration-300 translate-x-full opacity-0`;
        notification.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-${icons[type]} mr-3"></i>
                    <span class="font-medium">${message}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        container.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full', 'opacity-0');
            notification.classList.add('translate-x-0', 'opacity-100');
        }, 10);
        
        // Auto remove
        setTimeout(() => {
            notification.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => notification.remove(), 300);
        }, duration);
    }

    // Filter functions
    function filterByStatus(status) {
        const form = document.getElementById('filterForm');
        const statusSelect = form.querySelector('[name="status_stok"]');
        statusSelect.value = status.toLowerCase();
        form.submit();
    }

    function clearFilters() {
        window.location.href = "{{ route('kasir.daftar-produk') }}";
    }

    function removeFilter(filterName) {
        const url = new URL(window.location.href);
        url.searchParams.delete(filterName);
        window.location.href = url.toString();
    }

    function sortProducts(sortValue) {
        const url = new URL(window.location.href);
        url.searchParams.set('sort', sortValue);
        window.location.href = url.toString();
    }

    function refreshPage() {
        window.location.reload();
    }

    // Image error handling
    function handleImageError(imgElement, productId) {
        console.warn('Image failed to load for product:', productId);
        
        const parent = imgElement.parentElement;
        parent.innerHTML = `
            <div class="w-20 h-20 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg flex flex-col items-center justify-center mx-auto border-2 border-blue-200">
                <i class="fas fa-cube text-blue-400 text-lg mb-1"></i>
                <span class="text-xs text-blue-600 font-medium text-center px-1 leading-tight">No Image</span>
                <span class="text-xs text-red-500 mt-1" title="Gambar gagal dimuat">⚠️</span>
            </div>
        `;
    }

    // Product detail modal (placeholder)
    function showProductDetail(productId) {
        // Implement product detail modal here
        showNotification('Fitur detail produk akan datang', 'info', 3000);
    }

    // Update cart counter (if exists in layout)
    function updateCartCounter(count) {
        const cartCounters = document.querySelectorAll('.cart-counter');
        cartCounters.forEach(counter => {
            if (counter) {
                counter.textContent = count;
                counter.classList.remove('hidden');
                
                // Add animation
                counter.classList.add('animate-pulse');
                setTimeout(() => counter.classList.remove('animate-pulse'), 1000);
            }
        });
    }

    // Search debounce
    let searchTimeout;
    document.getElementById('searchInput')?.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (e.target.value.length >= 3 || e.target.value.length === 0) {
                document.getElementById('filterForm').submit();
            }
        }, 500);
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl+F untuk focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            document.getElementById('searchInput')?.focus();
        }
        
        // Escape untuk clear search
        if (e.key === 'Escape') {
            const searchInput = document.getElementById('searchInput');
            if (searchInput && searchInput.value) {
                searchInput.value = '';
                document.getElementById('filterForm').submit();
            }
        }
    });
</script>

<style>
    .btn-kasir-primary {
        @apply bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-2 rounded-lg transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2;
    }
    
    .btn-kasir-secondary {
        @apply bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-4 py-2 rounded-lg transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2;
    }
    
    .product-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .product-card:hover {
        transform: translateY(-4px);
    }
    
    /* Custom pagination styles */
    .pagination {
        @apply flex flex-wrap justify-center space-x-1;
    }
    
    .pagination .page-item {
        @apply inline-flex;
    }
    
    .pagination .page-link {
        @apply px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium transition-all duration-200;
    }
    
    .pagination .page-item.active .page-link {
        @apply bg-blue-600 text-white border-blue-600;
    }
    
    .pagination .page-item:not(.active) .page-link:hover {
        @apply bg-gray-100 border-gray-400;
    }
    
    .pagination .page-item.disabled .page-link {
        @apply text-gray-400 cursor-not-allowed bg-gray-100;
    }
</style>
@endsection