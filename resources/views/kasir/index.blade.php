@extends('layouts.kasir')

@section('title', 'Transaksi Kasir')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column - Input Produk & Keranjang -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Search Produk - VERSI SEDERHANA -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-search mr-2"></i>Cari Produk
            </h2>
            
            <!-- FORM 1: Quick Add to Cart (Pencarian langsung) -->
            <form method="POST" action="{{ route('kasir.quick-add-to-cart') }}" class="flex space-x-4 mb-4" id="quick-add-form">
                @csrf
                <input type="text" name="search" 
                    value="{{ session('search_query') }}"
                    placeholder="Scan barcode atau ketik nama produk..."
                    class="flex-1 border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    autocomplete="off"
                    autofocus
                    required
                    id="search-input">
                <button type="submit" class="btn-kasir-primary" id="quick-add-btn">
                    <i class="fas fa-plus mr-2"></i>Tambah ke Keranjang
                </button>
            </form>

            <!-- FORM 2: Quick Search (Tampilkan hasil) -->
            <form method="POST" action="{{ route('kasir.quick-search') }}" class="flex space-x-4" id="search-form">
                @csrf
                <input type="text" name="search" 
                    value="{{ session('search_query') }}"
                    placeholder="Atau cari dulu untuk melihat pilihan..."
                    class="flex-1 border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    autocomplete="off"
                    id="search-input-2">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold transition" id="search-btn">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
            </form>

            <!-- Loading Indicator -->
            <div id="search-loading" class="hidden mt-4 text-center py-4">
                <div class="inline-flex items-center">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500 mr-3"></div>
                    <span class="text-gray-600">Mencari produk...</span>
                </div>
            </div>

            <!-- Tampilkan hasil pencarian -->
            @if(session('search_results'))
                <div class="mt-4 border-t pt-4">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-semibold text-gray-800">Hasil Pencarian:</h3>
                        <a href="{{ route('kasir.clear-search-results') }}" class="text-sm text-red-500 hover:text-red-700">
                            <i class="fas fa-times mr-1"></i>Hapus Hasil
                        </a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-60 overflow-y-auto">
                        @foreach(session('search_results') as $product)
                            <div class="border border-gray-200 rounded-lg p-3 bg-white hover:bg-gray-50 transition">
                                <div class="font-semibold text-gray-800">{{ $product['nama_produk'] }}</div>
                                <div class="text-sm text-gray-600">
                                    <i class="fas fa-barcode mr-1"></i>{{ $product['barcode'] ?: 'No barcode' }}
                                </div>
                                <div class="text-green-600 font-bold text-lg">
                                    Rp {{ number_format($product['harga_jual'], 0, ',', '.') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    Stok: <span class="font-semibold {{ $product['stok_tersedia'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $product['stok_tersedia'] }}
                                    </span>
                                </div>
                                
                                <!-- FORM: Add to Cart dari hasil pencarian -->
                                <form method="POST" action="{{ route('kasir.add-to-cart') }}" class="mt-2 flex space-x-2 add-to-cart-form">
                                    @csrf
                                    <input type="hidden" name="produk_id" value="{{ $product['produk_id'] }}">
                                    <input type="number" name="qty" value="1" min="1" max="{{ $product['stok_tersedia'] }}" 
                                           class="w-20 border border-gray-300 rounded px-2 py-1 text-center quantity-input">
                                    <button type="submit" 
                                            class="flex-1 bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm font-semibold transition add-to-cart-btn">
                                        <i class="fas fa-cart-plus mr-1"></i>Tambah
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Alert Messages -->
            <div id="alert-container">
                @if(session('search_error'))
                    <div class="mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded alert-message">
                        <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('search_error') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="mt-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded alert-message">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded alert-message">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                    </div>
                @endif
            </div>

            <!-- Debug Info (Hapus di production) -->
            @if(app()->environment('local'))
                <div class="mt-4 p-2 bg-gray-100 rounded text-xs">
                    <strong>Debug Info:</strong> 
                    Cart Items: {{ count(session('cart', [])) }} | 
                    Search Results: {{ count(session('search_results', [])) }} |
                    Subtotal: Rp {{ number_format(session('subtotal', 0), 0, ',', '.') }}
                </div>
            @endif
        </div>

        <!-- Keranjang Belanja - BAGIAN YANG DIPERBAIKI -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-shopping-cart mr-2"></i>Keranjang Belanja
                </h2>
                <div class="flex items-center space-x-2">
                    <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                        {{ count(session('cart', [])) }} items
                    </span>
                    @if(!empty(session('cart')))
                        <a href="{{ route('kasir.reset-transaksi') }}" 
                           class="text-red-500 hover:text-red-700 text-sm"
                           onclick="return confirm('Yakin ingin menghapus semua item di keranjang?')">
                            <i class="fas fa-trash mr-1"></i>Hapus Semua
                        </a>
                    @endif
                </div>
            </div>

            <div class="space-y-3 max-h-96 overflow-y-auto" id="cart-container" style="max-height: 50vh;">
                @forelse(session('cart', []) as $index => $item)
                    <div class="border rounded-lg p-3 bg-white hover:bg-gray-50 transition cart-item">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="font-semibold text-gray-800">{{ $item['nama_produk'] }}</div>
                                <div class="text-sm text-gray-600">
                                    <i class="fas fa-barcode mr-1"></i>{{ $item['barcode'] }}
                                </div>
                                <div class="text-green-600 font-bold">
                                    Rp {{ number_format($item['harga_jual'], 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <!-- FORM UPDATE QUANTITY YANG DIPERBAIKI -->
                                <form method="POST" action="{{ route('kasir.update-cart-qty') }}" class="update-cart-form flex items-center space-x-2">
                                    @csrf
                                    <input type="hidden" name="index" value="{{ $index }}">
                                    
                                    <!-- Tombol Kurang -->
                                    <button type="button" class="bg-gray-200 w-8 h-8 rounded-full hover:bg-gray-300 flex items-center justify-center transition decrease-btn">
                                        <i class="fas fa-minus text-xs"></i>
                                    </button>
                                    
                                    <!-- Input Quantity yang bisa diketik -->
                                    <input type="number" 
                                           name="qty" 
                                           value="{{ $item['qty'] }}" 
                                           min="1" 
                                           max="999"
                                           class="w-16 border border-gray-300 rounded py-1 px-2 text-center quantity-input"
                                           data-index="{{ $index }}"
                                           onchange="updateQuantity({{ $index }}, this.value)">
                                    
                                    <!-- Tombol Tambah -->
                                    <button type="button" class="bg-gray-200 w-8 h-8 rounded-full hover:bg-gray-300 flex items-center justify-center transition increase-btn">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                    
                                    <!-- Tombol Hapus -->
                                    <button type="button" 
                                            class="text-red-500 ml-2 hover:text-red-700 transition remove-btn"
                                            onclick="removeItem({{ $index }}, '{{ $item['nama_produk'] }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="text-right font-semibold mt-2 text-gray-800 border-t pt-2">
                            Subtotal: Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-cart-plus text-4xl mb-2"></i>
                        <p>Belum ada produk di keranjang</p>
                        <p class="text-sm mt-2">Gunakan form pencarian di atas untuk menambahkan produk</p>
                    </div>
                @endforelse
            </div>

            <!-- Cart Summary -->
            @if(!empty(session('cart')))
                <div class="mt-4 border-t pt-4">
                    <div class="flex justify-between text-lg font-semibold">
                        <span>Total Keranjang:</span>
                        <span>Rp {{ number_format(session('subtotal', 0), 0, ',', '.') }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Right Column - Ringkasan & Pembayaran -->
    <div class="space-y-6">
        <!-- Ringkasan Transaksi -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Ringkasan</h2>

            <div class="space-y-3">
                <div class="flex justify-between">
                    <span>Subtotal:</span>
                    <span id="subtotal-display">Rp {{ number_format($subtotal ?? 0, 0, ',', '.') }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span>Diskon:</span>
                    <span id="diskon-display">- Rp {{ number_format($diskon ?? 0, 0, ',', '.') }}</span>
                </div>
                
                <hr>
                
                <div class="flex justify-between text-lg font-bold">
                    <span>Total Bayar:</span>
                    <span id="total-display" class="text-green-600">Rp {{ number_format($total_bayar ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Input Diskon -->
            <form method="POST" action="{{ route('kasir.apply-diskon') }}" class="mt-4" id="diskon-form">
                @csrf
                <label class="block text-sm font-medium text-gray-700 mb-2">Diskon (Rp)</label>
                <div class="flex space-x-2">
                    <input type="number" name="diskon" value="{{ $diskon ?? 0 }}" min="0" max="{{ $subtotal ?? 0 }}"
                        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        id="diskon-input">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold transition">
                        Apply
                    </button>
                </div>
                <div class="text-xs text-gray-500 mt-1">
                    Maksimal: Rp {{ number_format($subtotal ?? 0, 0, ',', '.') }}
                </div>
            </form>
        </div>

        <!-- Pembayaran -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Pembayaran</h2>

            <form method="POST" action="{{ route('kasir.proses-transaksi') }}" id="payment-form">
                @csrf
                
                <!-- Metode Pembayaran -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                    <select name="metode_pembayaran" id="metode-pembayaran" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="Tunai">Tunai</option>
                        <option value="Debit">Debit</option>
                        <option value="QRIS">QRIS</option>
                        <option value="Transfer">Transfer</option>
                    </select>
                </div>

                <!-- Jumlah Uang -->
                <div class="mb-4" id="cash-input-section">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Uang</label>
                    <input type="number" name="jumlah_uang" value="{{ old('jumlah_uang', $total_bayar ?? 0) }}" 
                        min="{{ $total_bayar ?? 0 }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        id="jumlah-uang-input">
                </div>

                <!-- Kembalian -->
                <div class="mb-4 p-3 bg-gray-50 rounded-lg" id="kembalian-section">
                    <div class="flex justify-between font-semibold">
                        <span>Kembalian:</span>
                        <span id="kembalian-display" class="text-green-600">
                            @php
                                $jumlah_uang = old('jumlah_uang', $total_bayar ?? 0);
                                $kembalian = max(0, $jumlah_uang - ($total_bayar ?? 0));
                            @endphp
                            Rp {{ number_format($kembalian, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="space-y-3">
                    <button type="submit" 
                            id="proses-transaksi-btn"
                            class="w-full btn-kasir-primary {{ empty(session('cart')) ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ empty(session('cart')) ? 'disabled' : '' }}>
                        <i class="fas fa-credit-card mr-2"></i>
                        <span id="proses-text">Proses Transaksi</span>
                        <div id="proses-loading" class="hidden inline-flex items-center">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                            Memproses...
                        </div>
                    </button>
                    
                    <a href="{{ route('kasir.reset-transaksi') }}" 
                       class="w-full bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg transition block text-center font-semibold"
                       onclick="return confirm('Yakin ingin mereset transaksi? Semua item di keranjang akan dihapus.')">
                        <i class="fas fa-redo mr-2"></i>Reset Transaksi
                    </a>
                    
                    <a href="{{ route('kasir.daftar-produk') }}" 
                       class="w-full bg-purple-500 hover:bg-purple-600 text-white py-2 px-4 rounded-lg transition block text-center font-semibold">
                        <i class="fas fa-list mr-2"></i>Lihat Semua Produk
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
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
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-kasir-primary:hover:not(:disabled) {
        background: linear-gradient(135deg, #059669, #047857);
        transform: translateY(-2px);
        box-shadow: 0 6px 8px rgba(16, 185, 129, 0.4);
    }
    
    .btn-kasir-primary:disabled {
        cursor: not-allowed;
        opacity: 0.5;
        transform: none;
    }
    
    .alert-message {
        animation: slideIn 0.3s ease-out;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .cart-item {
        animation: fadeIn 0.3s ease-out;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* Style untuk input quantity */
    .quantity-input {
        -moz-appearance: textfield;
    }
    
    .quantity-input::-webkit-outer-spin-button,
    .quantity-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const totalBayar = {{ $total_bayar ?? 0 }};
    const subtotal = {{ $subtotal ?? 0 }};
    const diskon = {{ $diskon ?? 0 }};
    
    const elements = {
        jumlahUangInput: document.getElementById('jumlah-uang-input'),
        kembalianDisplay: document.getElementById('kembalian-display'),
        metodePembayaranSelect: document.getElementById('metode-pembayaran'),
        cashInputSection: document.getElementById('cash-input-section'),
        kembalianSection: document.getElementById('kembalian-section'),
        prosesTransaksiBtn: document.getElementById('proses-transaksi-btn'),
        paymentForm: document.getElementById('payment-form'),
        searchForms: document.querySelectorAll('form[id$="form"]'),
        alertContainer: document.getElementById('alert-container')
    };

    // Initialize
    initPaymentSection();
    setupEventListeners();
    setupFormSubmissions();
    setupCartInteractions();

    function initPaymentSection() {
        updatePaymentUI();
        hitungKembalian();
    }

    function setupEventListeners() {
        // Payment events
        elements.jumlahUangInput?.addEventListener('input', hitungKembalian);
        elements.metodePembayaranSelect?.addEventListener('change', updatePaymentUI);
        
        // Auto-remove alerts after 5 seconds
        autoRemoveAlerts();
    }

    function setupFormSubmissions() {
        // Prevent double submission
        elements.paymentForm?.addEventListener('submit', function(e) {
            const btn = elements.prosesTransaksiBtn;
            if (btn && !btn.disabled) {
                btn.disabled = true;
                btn.querySelector('#proses-text').classList.add('hidden');
                btn.querySelector('#proses-loading').classList.remove('hidden');
            }
        });

        // Add loading to search forms
        elements.searchForms?.forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                if (this.id === 'search-form') {
                    document.getElementById('search-loading')?.classList.remove('hidden');
                }
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Loading...';
                
                // Revert after 10 seconds if still loading
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    document.getElementById('search-loading')?.classList.add('hidden');
                }, 10000);
            });
        });
    }

    // FUNGSI BARU: Setup interaksi keranjang
    function setupCartInteractions() {
        // Event delegation untuk tombol tambah/kurang
        document.getElementById('cart-container')?.addEventListener('click', function(e) {
            const target = e.target;
            const cartItem = target.closest('.cart-item');
            
            if (!cartItem) return;
            
            const index = cartItem.querySelector('.quantity-input').dataset.index;
            const quantityInput = cartItem.querySelector('.quantity-input');
            let currentQty = parseInt(quantityInput.value);
            
            // Tombol tambah
            if (target.closest('.increase-btn')) {
                quantityInput.value = currentQty + 1;
                updateQuantity(index, quantityInput.value);
            }
            
            // Tombol kurang
            if (target.closest('.decrease-btn')) {
                if (currentQty > 1) {
                    quantityInput.value = currentQty - 1;
                    updateQuantity(index, quantityInput.value);
                }
            }
        });
        
        // Event untuk input quantity (on change)
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function() {
                const index = this.dataset.index;
                updateQuantity(index, this.value);
            });
            
            // Validasi input
            input.addEventListener('blur', function() {
                if (!this.value || parseInt(this.value) < 1) {
                    this.value = 1;
                    updateQuantity(this.dataset.index, 1);
                }
            });
        });
    }

    // FUNGSI BARU: Update quantity via AJAX
    function updateQuantity(index, newQty) {
        if (newQty < 1) newQty = 1;
        
        // Tampilkan loading
        const cartItem = document.querySelector(`.quantity-input[data-index="${index}"]`).closest('.cart-item');
        const originalContent = cartItem.innerHTML;
        cartItem.innerHTML = '<div class="text-center py-2"><div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500 mx-auto"></div><div class="text-sm text-gray-600 mt-1">Updating...</div></div>';
        
        // Kirim request AJAX
        fetch('{{ route("kasir.update-cart-qty") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                index: parseInt(index),
                qty: parseInt(newQty)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload halaman untuk update total
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat update quantity');
            window.location.reload();
        });
    }

    // FUNGSI BARU: Hapus item
    function removeItem(index, productName) {
        if (!confirm(`Hapus "${productName}" dari keranjang?`)) return;
        
        // Tampilkan loading
        const cartItem = document.querySelector(`.quantity-input[data-index="${index}"]`).closest('.cart-item');
        const originalContent = cartItem.innerHTML;
        cartItem.innerHTML = '<div class="text-center py-2"><div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500 mx-auto"></div><div class="text-sm text-gray-600 mt-1">Menghapus...</div></div>';
        
        // Kirim request AJAX
        fetch('{{ route("kasir.remove-from-cart") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                index: parseInt(index)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus item');
            window.location.reload();
        });
    }

    function updatePaymentUI() {
        const metode = elements.metodePembayaranSelect.value;
        const isTunai = metode === 'Tunai';
        
        // Tampilkan/sembunyikan section tunai
        elements.cashInputSection.style.display = isTunai ? 'block' : 'none';
        elements.kembalianSection.style.display = isTunai ? 'block' : 'none';
        
        if (!isTunai) {
            elements.jumlahUangInput.value = totalBayar;
            elements.jumlahUangInput.min = totalBayar;
        } else {
            elements.jumlahUangInput.min = totalBayar;
        }
        
        hitungKembalian();
    }

    function hitungKembalian() {
        const metode = elements.metodePembayaranSelect.value;
        const jumlahUang = parseFloat(elements.jumlahUangInput.value) || 0;
        
        let kembalian = 0;
        let displayText = '';
        let textColor = 'text-green-600';
        
        if (metode === 'Tunai') {
            kembalian = Math.max(0, jumlahUang - totalBayar);
            
            if (jumlahUang < totalBayar) {
                displayText = `Rp ${formatRupiah(kembalian)} <span class="text-xs text-red-600">(Uang kurang Rp ${formatRupiah(totalBayar - jumlahUang)})</span>`;
                textColor = 'text-red-600';
            } else {
                displayText = `Rp ${formatRupiah(kembalian)}`;
                textColor = 'text-green-600';
            }
        } else {
            displayText = 'Rp 0';
            textColor = 'text-gray-600';
        }
        
        elements.kembalianDisplay.innerHTML = displayText;
        elements.kembalianDisplay.className = `font-semibold ${textColor}`;
    }

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }

    function autoRemoveAlerts() {
        setTimeout(() => {
            document.querySelectorAll('.alert-message').forEach(alert => {
                alert.style.transition = 'all 0.3s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Focus search input dengan Ctrl+K
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            document.getElementById('search-input')?.focus();
        }
        
        // Process transaction dengan Ctrl+Enter
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            if (!elements.prosesTransaksiBtn.disabled) {
                elements.paymentForm?.requestSubmit();
            }
        }
    });
});
</script>
@endsection