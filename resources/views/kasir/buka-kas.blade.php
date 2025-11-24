@extends('layouts.kasir')

@section('title', 'Buka Kas Harian - SmartMart Campus')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="text-center mb-6">
                <i class="fas fa-cash-register text-4xl text-green-500 mb-3"></i>
                <h1 class="text-2xl font-bold text-gray-800">Buka Kas Harian</h1>
                <p class="text-gray-600 mt-2">
                    <i class="fas fa-calendar mr-2"></i>
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </p>
            </div>

            <!-- Info Saldo Rekomendasi -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-blue-500 mr-3 text-xl"></i>
                    <div>
                        <p class="font-semibold text-blue-800">Saldo Rekomendasi</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">
                            Rp {{ number_format($saldoRekomendasi, 0, ',', '.') }}
                        </p>
                        <p class="text-sm text-blue-600 mt-1">
                            Berdasarkan saldo akhir kemarin
                        </p>
                    </div>
                </div>
            </div>

            <form id="bukaKasForm">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-wallet mr-2"></i>
                        Saldo Awal Hari Ini
                    </label>
                    <input type="number"
                        name="saldo_awal"
                        id="saldo_awal"
                        value="{{ $saldoRekomendasi }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                        placeholder="Masukkan saldo awal"
                        min="0"
                        step="1000"
                        required>
                    <div class="flex justify-between mt-2">
                        <button type="button" onclick="setSaldo('rekomendasi')"
                            class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200 transition">
                            Gunakan Rekomendasi
                        </button>
                        <button type="button" onclick="setSaldo('custom')"
                            class="text-xs bg-orange-100 text-orange-700 px-2 py-1 rounded hover:bg-orange-200 transition">
                            Atur Manual
                        </button>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-sticky-note mr-2"></i>
                        Keterangan (Opsional)
                    </label>
                    <textarea name="keterangan"
                        id="keterangan"
                        rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                        placeholder="Catatan untuk pembukaan kas hari ini..."></textarea>
                </div>

                <!-- Info Selisih -->
                <div id="selisihInfo" class="hidden mb-4 p-3 rounded-lg bg-gray-50 border">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Selisih:</span>
                        <span id="selisihText" class="text-lg font-bold"></span>
                    </div>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('kasir.dashboard') }}"
                        class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-center font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                    <button type="submit"
                        id="submitBtn"
                        class="flex-1 px-4 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition font-medium">
                        <i class="fas fa-lock-open mr-2"></i>
                        Buka Kas
                    </button>
                </div>
            </form>
        </div>

        <!-- Info Tambahan -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mt-4 border-l-4 border-blue-500">
            <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                <i class="fas fa-lightbulb mr-2 text-blue-500"></i>
                Informasi Sistem Hybrid
            </h3>
            <div class="space-y-2 text-sm text-gray-600">
                <div class="flex items-start">
                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                    <span><strong>Otomatis:</strong> Sistem menyarankan saldo berdasarkan saldo kemarin</span>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-edit text-orange-500 mr-2 mt-1"></i>
                    <span><strong>Fleksibel:</strong> Anda bisa mengatur saldo manual sesuai kebutuhan</span>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-shield-alt text-purple-500 mr-2 mt-1"></i>
                    <span><strong>Aman:</strong> Selisih saldo akan tercatat dalam keterangan</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-green-500"></div>
        <span class="text-gray-700 font-medium">Membuka Kas...</span>
    </div>
</div>

<!-- Notification Container -->
<div id="notificationContainer" class="fixed top-4 right-4 z-50 space-y-2 max-w-sm"></div>

<script>
// ✅ DEKLARASI FUNGSI DI LEVEL GLOBAL
window.saldoRekomendasi = {{ $saldoRekomendasi }};

// ✅ FUNGSI SET SALDO
function setSaldo(type) {
    const input = document.getElementById('saldo_awal');
    if (!input) {
        console.error('❌ Input saldo_awal tidak ditemukan');
        showNotification('Error: Input saldo tidak ditemukan', 'error');
        return;
    }

    if (type === 'rekomendasi') {
        input.value = window.saldoRekomendasi;
    } else {
        input.value = '';
        input.focus();
    }

    // Trigger input event untuk update selisih
    if (input) {
        const inputEvent = new Event('input');
        input.dispatchEvent(inputEvent);
    }
}

// ✅ FUNGSI BUKA KAS
function bukaKas() {
    console.log('=== MEMULAI PROSES BUKA KAS ===');

    // Dapatkan semua elemen
    const saldoAwalInput = document.getElementById('saldo_awal');
    const keteranganInput = document.getElementById('keterangan');
    const submitBtn = document.getElementById('submitBtn');
    const loadingOverlay = document.getElementById('loadingOverlay');

    // Validasi elemen penting
    if (!saldoAwalInput || !submitBtn || !loadingOverlay) {
        console.error('❌ Elemen penting tidak ditemukan');
        showNotification('Error sistem: Form tidak lengkap', 'error');
        return;
    }

    const saldoAwal = saldoAwalInput.value;
    const keterangan = keteranganInput ? keteranganInput.value : '';

    // Validasi input
    if (!saldoAwal || saldoAwal <= 0) {
        showNotification('Saldo awal harus diisi dan lebih dari 0', 'error');
        return;
    }

    // Disable submit button dan tampilkan loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
    loadingOverlay.classList.remove('hidden');

    // Prepare form data
    const formData = new FormData();
    formData.append('saldo_awal', saldoAwal);
    formData.append('keterangan', keterangan);
    formData.append('_token', '{{ csrf_token() }}');

    // Kirim request
    fetch('{{ route("kasir.kas-harian.buka") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(errorData => {
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification('✅ Kas berhasil dibuka!', 'success');
            setTimeout(() => {
                window.location.href = '{{ route("kasir.dashboard") }}';
            }, 1500);
        } else {
            throw new Error(data.message || 'Gagal membuka kas');
        }
    })
    .catch(error => {
        console.error('❌ Error:', error);
        showNotification('❌ ' + error.message, 'error');
    })
    .finally(() => {
        // Reset button state
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-lock-open mr-2"></i>Buka Kas';
        }
        // Sembunyikan loading
        if (loadingOverlay) {
            loadingOverlay.classList.add('hidden');
        }
    });
}

// ✅ FUNGSI NOTIFICATION
function showNotification(message, type = 'success') {
    const container = document.getElementById('notificationContainer');
    if (!container) {
        alert(message); // Fallback
        return;
    }

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
        if (notification.parentNode) {
            notification.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => notification.remove(), 300);
        }
    }, 4000);
}

// ✅ INITIALIZATION SETELAH DOM LOADED
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Buka Kas Form Initialized');
    
    const saldoAwalInput = document.getElementById('saldo_awal');
    const selisihInfo = document.getElementById('selisihInfo');
    const selisihText = document.getElementById('selisihText');
    const bukaKasForm = document.getElementById('bukaKasForm');

    // Event listener untuk input saldo
    if (saldoAwalInput && selisihInfo && selisihText) {
        saldoAwalInput.addEventListener('input', function() {
            const saldoAktual = parseFloat(this.value) || 0;
            const selisih = saldoAktual - window.saldoRekomendasi;

            if (selisih !== 0) {
                selisihInfo.classList.remove('hidden');
                if (selisih > 0) {
                    selisihText.textContent = '+Rp ' + Math.abs(selisih).toLocaleString('id-ID');
                    selisihText.className = 'text-lg font-bold text-green-600';
                } else {
                    selisihText.textContent = '-Rp ' + Math.abs(selisih).toLocaleString('id-ID');
                    selisihText.className = 'text-lg font-bold text-red-600';
                }
            } else {
                selisihInfo.classList.add('hidden');
            }
        });
    }

    // Form submission
    if (bukaKasForm) {
        bukaKasForm.addEventListener('submit', function(e) {
            e.preventDefault();
            bukaKas();
        });
    }
});
</script>
@endsection