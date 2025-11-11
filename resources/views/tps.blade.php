@extends('layout')

@section('title', 'TPS - Kasir')

@section('header', 'Transaksi Penjualan (TPS)')

@section('content')
<div class="grid grid-cols-12 gap-6">

    <div class="col-span-12 lg:col-span-7">
        <div class="mb-4">
            <input type="text" id="barcode-input" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg text-lg focus:ring-blue-500 focus:border-blue-500" 
                   placeholder="Scan barcode atau ketik nama produk..." autofocus>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($fastGridProducts as $product)
            <button class="fast-grid-item bg-white p-4 rounded-lg shadow border border-gray-200 hover:bg-gray-50 text-left" 
                    data-barcode="{{ $product->barcode }}"> <div class="flex justify-between items-center mb-2">
                    <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">Stok: {{ $product->stok_tersedia }}</span> </div>
                <h3 class="font-semibold text-gray-800">{{ $product->nama_produk }}</h3> <p class="text-xs text-gray-500">{{ $product->kategori->nama_kategori ?? 'Umum' }}</p> <p class="text-lg font-bold text-blue-600 mt-2">Rp {{ number_format($product->harga_jual, 0, ',', '.') }}</p> </button>
            @endforeach
        </div>
    </div>

    <div class="col-span-12 lg:col-span-5">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden h-full flex flex-col">
            <div class="p-4 border-b">
                <h2 class="text-xl font-semibold">Keranjang Belanja</h2>
            </div>
            
            <div id="cart-items-list" class="flex-grow p-4 overflow-y-auto">
                <div id="cart-empty" class="flex flex-col items-center justify-center text-gray-400 h-full">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <p class="mt-2">Keranjang masih kosong</p>
                </div>
                </div>
            
            <div class="p-4 bg-gray-50 border-t">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-lg font-semibold text-gray-700">Total Belanja:</span>
                    <span id="total-belanja" class="text-2xl font-bold text-blue-600">Rp 0</span>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran:</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button data-payment="Tunai" class="payment-method-btn ring-2 ring-blue-500 bg-blue-100 text-blue-700 p-2 rounded-lg text-sm font-semibold">Tunai</button>
                        <button data-payment="QRIS" class="payment-method-btn bg-gray-200 text-gray-700 p-2 rounded-lg text-sm font-semibold hover:bg-gray-300">QRIS</button>
                        <button data-payment="Debit" class="payment-method-btn bg-gray-200 text-gray-700 p-2 rounded-lg text-sm font-semibold hover:bg-gray-300">Debit</button>
                    </div>
                </div>

                <div id="tunai-input-group" class="mb-4">
                    <label for="jumlah-bayar" class="block text-sm font-medium text-gray-700">Jumlah Bayar (Tunai):</label>
                    <input type="number" id="jumlah-bayar" class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="0">
                    <div class="flex justify-between text-sm text-gray-600 mt-1">
                        <span>Kembalian:</span>
                        <span id="kembalian" class="font-semibold">Rp 0</span>
                    </div>
                </div>

                <button id="btn-proses-pembayaran" class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition duration-300">
                    Proses Pembayaran
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // === STATE ===
    let cart = []; // Array untuk menyimpan item: { id, barcode, name, price, quantity }
    let paymentMethod = 'Tunai';
    let totalAmount = 0;

    // === SELECTORS ===
    const barcodeInput = document.getElementById('barcode-input');
    const cartItemsList = document.getElementById('cart-items-list');
    const cartEmpty = document.getElementById('cart-empty');
    const totalBelanjaEl = document.getElementById('total-belanja');
    const paymentButtons = document.querySelectorAll('.payment-method-btn');
    const tunaiInputGroup = document.getElementById('tunai-input-group');
    const jumlahBayarInput = document.getElementById('jumlah-bayar');
    const kembalianEl = document.getElementById('kembalian');
    const btnProses = document.getElementById('btn-proses-pembayaran');
    const fastGridItems = document.querySelectorAll('.fast-grid-item');

    // === FUNCTIONS ===

    // Fungsi untuk format Rupiah
    const formatRupiah = (angka) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);

    // Fungsi untuk mencari produk di API
    const fetchProduct = async (barcode) => {
        if (!barcode) return;
        try {
            // Panggil API Laravel yang sudah kita buat
            const response = await fetch(`/api/produk/${barcode}`);
            if (!response.ok) {
                alert('Produk tidak ditemukan!');
                return;
            }
            const product = await response.json();
            addProductToCart(product);
        } catch (error) {
            console.error('Error fetching product:', error);
            alert('Gagal mengambil data produk.');
        }
    };

    // Fungsi untuk menambah produk ke keranjang
    const addProductToCart = (product) => {
        // Menggunakan primary key 'produk_id'
        const existingItem = cart.find(item => item.produk_id === product.produk_id); 

        if (existingItem) {
            // Menggunakan 'stok_tersedia'
            if (existingItem.quantity < product.stok_tersedia) {
                existingItem.quantity++;
            } else {
                alert(`Stok ${product.nama_produk} tidak mencukupi (tersisa ${product.stok_tersedia})`);
            }
        } else {
            if (product.stok_tersedia > 0) {
                cart.push({
                    produk_id: product.produk_id, // DIUBAH
                    barcode: product.barcode,
                    name: product.nama_produk, // DIUBAH
                    price: parseFloat(product.harga_jual), // DIUBAH
                    quantity: 1,
                    stock: product.stok_tersedia // DIUBAH
                });
            } else {
                 alert(`Stok ${product.nama_produk} habis`);
            }
        }
        renderCart();
    };

    // Fungsi untuk update jumlah (dipanggil dari tombol +/-/input)
    const updateQuantity = (id, newQuantity) => {
        const item = cart.find(item => item.produk_id == id); // DIUBAH
        if (!item) return;

        newQuantity = parseInt(newQuantity);

        if (newQuantity < 1) {
            cart = cart.filter(item => item.produk_id != id); // DIUBAH
        } else if (newQuantity > item.stock) {
            alert(`Stok tidak mencukupi (tersisa ${item.stock})`);
            item.quantity = item.stock; 
        } else {
            item.quantity = newQuantity;
        }
        renderCart();
    };
    
    // Fungsi untuk hapus item
    const removeItem = (id) => {
        cart = cart.filter(item => item.produk_id != id); // DIUBAH
        renderCart();
    };

    // Fungsi untuk merender ulang tampilan keranjang
    const renderCart = () => {
        // Kosongkan list
        cartItemsList.innerHTML = '';
        
        if (cart.length === 0) {
            cartItemsList.appendChild(cartEmpty);
        } else {
            totalAmount = 0;
            cart.forEach(item => {
                const subtotal = item.price * item.quantity;
                totalAmount += subtotal;

                const itemEl = document.createElement('div');
                itemEl.className = 'flex items-center justify-between py-3 border-b border-gray-200';
                itemEl.innerHTML = `
                    <div class="flex-grow">
                        <p class="font-semibold text-gray-800">${item.name}</p>
                        <p class="text-sm text-gray-500">${formatRupiah(item.price)}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="cart-btn-qty" data-id="${item.produk_id}" data-action="decrease">-</button> <input type="number" class="cart-input-qty w-12 text-center border rounded" value="${item.quantity}" data-id="${item.produk_id}" min="1"> <button class="cart-btn-qty" data-id="${item.produk_id}" data-action="increase">+</button> </div>
                    <p class="w-24 text-right font-semibold text-gray-700">${formatRupiah(subtotal)}</p>
                    <button class="cart-btn-remove ml-3 text-red-500 hover:text-red-700" data-id="${item.produk_id}">&times;</button> 
                    `;
                cartItemsList.appendChild(itemEl);
            });
        }
        
        // Update total
        totalBelanjaEl.textContent = formatRupiah(totalAmount);
        updateKembalian();
    };
    
    // Fungsi update kembalian
    const updateKembalian = () => {
        if (paymentMethod === 'Tunai') {
            const bayar = parseFloat(jumlahBayarInput.value) || 0;
            const kembalian = bayar - totalAmount;
            kembalianEl.textContent = (kembalian >= 0) ? formatRupiah(kembalian) : formatRupiah(0);
        } else {
            kembalianEl.textContent = formatRupiah(0);
        }
    };

    // Fungsi proses pembayaran
        const prosesPembayaran = async () => {
        // ... (validasi di atasnya sama) ...
        const bayar = parseFloat(jumlahBayarInput.value) || 0; // Ambil jumlah bayar
        // ... (validasi tunai sama) ...
        
        btnProses.disabled = true;
        btnProses.textContent = 'Memproses...';

        try {
            const response = await fetch('/api/transaksi', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    total_amount: totalAmount,
                    payment_method: paymentMethod,
                    jumlah_bayar: bayar, // DIUBAH: Kirim jumlah bayar
                    cart: cart.map(item => ({ produk_id: item.produk_id, quantity: item.quantity })) // DIUBAH: produk_id
                })
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Gagal menyimpan transaksi');
            }
            
            alert(`Transaksi berhasil! ID: ${result.transaction_id}`);
            // Reset semua
            cart = [];
            totalAmount = 0;
            jumlahBayarInput.value = '';
            renderCart();

        } catch (error) {
            console.error('Error proses pembayaran:', error);
            alert(`Error: ${error.message}`);
        } finally {
            btnProses.disabled = false;
            btnProses.textContent = 'Proses Pembayaran';
        }
    };


    // === EVENT LISTENERS ===

    // 1. Input Barcode
    barcodeInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            fetchProduct(e.target.value);
            e.target.value = ''; // Kosongkan input
        }
    });
    
    // 2. Klik Grid Cepat
    fastGridItems.forEach(item => {
        item.addEventListener('click', () => {
            fetchProduct(item.dataset.barcode);
        });
    });

    // 3. Klik Tombol di Keranjang (Event Delegation)
    cartItemsList.addEventListener('click', (e) => {
        const target = e.target;
        const id = target.dataset.id;
        
        if (target.classList.contains('cart-btn-qty')) {
            const action = target.dataset.action;
            const item = cart.find(i => i.id == id);
            if (action === 'increase') {
                updateQuantity(item.id, item.quantity + 1);
            } else if (action === 'decrease') {
                updateQuantity(item.id, item.quantity - 1);
            }
        }
        
        if (target.classList.contains('cart-btn-remove')) {
            removeItem(id);
        }
    });
    
    // 4. Ubah manual input kuantitas
    cartItemsList.addEventListener('change', (e) => {
         if (e.target.classList.contains('cart-input-qty')) {
            const id = e.target.dataset.id;
            updateQuantity(id, e.target.value);
         }
    });

    // 5. Ganti Metode Bayar
    paymentButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            // Hapus style aktif dari semua tombol
            paymentButtons.forEach(b => b.classList.remove('ring-2', 'ring-blue-500', 'bg-blue-100', 'text-blue-700') & b.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300'));
            // Tambah style aktif ke tombol yg diklik
            btn.classList.add('ring-2', 'ring-blue-500', 'bg-blue-100', 'text-blue-700');
            btn.classList.remove('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
            
            paymentMethod = btn.dataset.payment;
            
            // Tampilkan/sembunyikan input tunai
            if (paymentMethod === 'Tunai') {
                tunaiInputGroup.style.display = 'block';
            } else {
                tunaiInputGroup.style.display = 'none';
            }
            updateKembalian();
        });
    });
    
    // 6. Input Jumlah Bayar (Tunai)
    jumlahBayarInput.addEventListener('input', updateKembalian);

    // 7. Tombol Proses Pembayaran
    btnProses.addEventListener('click', prosesPembayaran);
    
    // Inisialisasi awal
    renderCart();
});
</script>
@endpush