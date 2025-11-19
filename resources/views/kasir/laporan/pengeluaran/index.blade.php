<!-- resources/views/kasir/pengeluaran/index.blade.php -->
@extends('layouts.kasir')

@section('title', 'Manajemen Pengeluaran - SmartMart Campus')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-money-bill-wave text-primary mr-2"></i>
                Manajemen Pengeluaran
            </h1>
            <p class="mb-0">Kelola pengeluaran kas harian</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" onclick="showTambahPengeluaranModal()">
                <i class="fas fa-plus mr-2"></i>Tambah Pengeluaran
            </button>
            <button class="btn btn-outline-secondary" onclick="showLaporanModal()">
                <i class="fas fa-chart-bar mr-2"></i>Laporan
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Pengeluaran Hari Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-pengeluaran-hari-ini">
                                Rp 0
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Saldo Tersedia
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="saldo-tersedia">
                                Rp 0
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Jumlah Transaksi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="jumlah-transaksi">
                                0
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Kategori Terbanyak
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="kategori-terbanyak">
                                -
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list mr-2"></i>Daftar Pengeluaran Hari Ini
                    </h6>
                    <div class="d-flex gap-2">
                        <input type="date" class="form-control form-control-sm" id="filter-tanggal" 
                               value="{{ date('Y-m-d') }}" style="max-width: 150px;">
                        <button class="btn btn-sm btn-outline-secondary" onclick="refreshData()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="table-pengeluaran" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kategori</th>
                                    <th>Keterangan</th>
                                    <th>Jumlah</th>
                                    <th>Dibuat Oleh</th>
                                    <th>Waktu</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="pengeluaran-body">
                                <!-- Data akan diisi via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Pengeluaran -->
<div class="modal fade" id="tambahPengeluaranModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus mr-2"></i>Tambah Pengeluaran Baru
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-tambah-pengeluaran">
                <div class="modal-body">
                    <div id="modal-alert"></div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kategori">Kategori *</label>
                                <select class="form-control" id="kategori" name="kategori" required>
                                    <option value="">Pilih Kategori</option>
                                    <!-- Options akan diisi via JavaScript -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal">Tanggal *</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" 
                                       value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="keterangan">Keterangan *</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" 
                                  rows="3" placeholder="Deskripsi pengeluaran..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="jumlah">Jumlah (Rp) *</label>
                        <input type="number" class="form-control" id="jumlah" name="jumlah" 
                               min="1000" step="500" placeholder="0" required>
                        <small class="form-text text-muted">
                            Saldo tersedia: <span id="saldo-tersedia-modal" class="font-weight-bold">Rp 0</span>
                        </small>
                    </div>

                    <input type="hidden" id="kas_harian_id" name="kas_harian_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-simpan">
                        <i class="fas fa-save mr-2"></i>Simpan Pengeluaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Laporan -->
<div class="modal fade" id="laporanModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-chart-bar mr-2"></i>Laporan Pengeluaran
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Filter Form -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label>Tanggal Mulai</label>
                        <input type="date" class="form-control" id="start_date" 
                               value="{{ date('Y-m-01') }}">
                    </div>
                    <div class="col-md-3">
                        <label>Tanggal Akhir</label>
                        <input type="date" class="form-control" id="end_date" 
                               value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <label>Kategori</label>
                        <select class="form-control" id="filter_kategori">
                            <option value="">Semua Kategori</option>
                            <!-- Options akan diisi via JavaScript -->
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary w-100" onclick="loadLaporan()">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                    </div>
                </div>

                <!-- Laporan Content -->
                <div id="laporan-content">
                    <!-- Laporan akan diisi via JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Global variables
let currentKasHarian = null;
let kategoriList = {};

document.addEventListener('DOMContentLoaded', function() {
    initializePengeluaran();
});

function initializePengeluaran() {
    loadData();
    setupEventListeners();
}

function setupEventListeners() {
    // Filter tanggal change
    document.getElementById('filter-tanggal').addEventListener('change', function() {
        loadData();
    });

    // Form submission
    document.getElementById('form-tambah-pengeluaran').addEventListener('submit', function(e) {
        e.preventDefault();
        simpanPengeluaran();
    });

    // Real-time saldo check
    document.getElementById('jumlah').addEventListener('input', function() {
        checkSaldo();
    });
}

function loadData() {
    const tanggal = document.getElementById('filter-tanggal').value;
    
    showLoading();
    
    fetch(`{{ route('kasir.pengeluaran.index') }}?tanggal=${tanggal}`)
        .then(response => response.json())
        .then(data => {
            hideLoading();
            
            if (data.success) {
                updateUI(data);
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            hideLoading();
            showAlert('error', 'Terjadi kesalahan saat memuat data');
            console.error('Error:', error);
        });
}

function updateUI(data) {
    // Update stats
    document.getElementById('total-pengeluaran-hari-ini').textContent = 
        formatRupiah(data.total_pengeluaran);
    document.getElementById('saldo-tersedia').textContent = 
        formatRupiah(data.kas_harian.saldo_akhir);
    document.getElementById('jumlah-transaksi').textContent = 
        data.pengeluaran.length;
    
    // Update kategori terbanyak
    if (data.kategori_summary.length > 0) {
        const topKategori = data.kategori_summary[0];
        document.getElementById('kategori-terbanyak').textContent = 
            `${topKategori.kategori} (${formatRupiah(topKategori.total)})`;
    }

    // Update table
    renderTable(data.pengeluaran);
    
    // Store current kas harian
    currentKasHarian = data.kas_harian;
}

function renderTable(pengeluaran) {
    const tbody = document.getElementById('pengeluaran-body');
    
    if (pengeluaran.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4 text-muted">
                    <i class="fas fa-inbox fa-2x mb-3"></i><br>
                    Tidak ada pengeluaran untuk tanggal ini
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = pengeluaran.map(item => `
        <tr>
            <td>${formatTanggal(item.tanggal)}</td>
            <td>
                <span class="badge badge-info">${item.kategori}</span>
            </td>
            <td>${item.keterangan}</td>
            <td class="font-weight-bold text-danger">${formatRupiah(item.jumlah)}</td>
            <td>${item.user?.nama_lengkap || 'System'}</td>
            <td>${formatWaktu(item.created_at)}</td>
            <td>
                <button class="btn btn-sm btn-outline-danger" onclick="hapusPengeluaran(${item.id})" 
                        title="Hapus Pengeluaran" ${!item.can_cancel ? 'disabled' : ''}>
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

function showTambahPengeluaranModal() {
    if (!currentKasHarian) {
        showAlert('error', 'Kas harian tidak tersedia');
        return;
    }

    if (currentKasHarian.status !== 'Open') {
        showAlert('error', 'Tidak dapat menambah pengeluaran karena kas sudah ditutup');
        return;
    }

    showLoading();
    
    fetch('{{ route("kasir.pengeluaran.create") }}')
        .then(response => response.json())
        .then(data => {
            hideLoading();
            
            if (data.success) {
                setupTambahModal(data);
                $('#tambahPengeluaranModal').modal('show');
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            hideLoading();
            showAlert('error', 'Terjadi kesalahan');
            console.error('Error:', error);
        });
}

function setupTambahModal(data) {
    // Reset form
    document.getElementById('form-tambah-pengeluaran').reset();
    document.getElementById('modal-alert').innerHTML = '';
    
    // Set kas harian ID
    document.getElementById('kas_harian_id').value = data.kas_harian.id;
    document.getElementById('saldo-tersedia-modal').textContent = 
        formatRupiah(data.saldo_tersedia);
    
    // Setup kategori options
    const kategoriSelect = document.getElementById('kategori');
    kategoriSelect.innerHTML = '<option value="">Pilih Kategori</option>';
    
    kategoriList = data.kategori;
    for (const [key, value] of Object.entries(data.kategori)) {
        kategoriSelect.innerHTML += `<option value="${key}">${value}</option>`;
    }
}

function checkSaldo() {
    const jumlah = parseFloat(document.getElementById('jumlah').value) || 0;
    const saldoTersedia = parseFloat(currentKasHarian.saldo_akhir);
    
    if (jumlah > saldoTersedia) {
        document.getElementById('jumlah').classList.add('is-invalid');
        document.getElementById('btn-simpan').disabled = true;
    } else {
        document.getElementById('jumlah').classList.remove('is-invalid');
        document.getElementById('btn-simpan').disabled = false;
    }
}

function simpanPengeluaran() {
    const formData = new FormData(document.getElementById('form-tambah-pengeluaran'));
    const btnSimpan = document.getElementById('btn-simpan');
    
    btnSimpan.disabled = true;
    btnSimpan.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
    
    fetch('{{ route("kasir.pengeluaran.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message, 'modal-alert');
            $('#tambahPengeluaranModal').modal('hide');
            loadData(); // Reload data
        } else {
            showAlert('error', data.message, 'modal-alert');
        }
    })
    .catch(error => {
        showAlert('error', 'Terjadi kesalahan saat menyimpan', 'modal-alert');
        console.error('Error:', error);
    })
    .finally(() => {
        btnSimpan.disabled = false;
        btnSimpan.innerHTML = '<i class="fas fa-save mr-2"></i>Simpan Pengeluaran';
    });
}

function hapusPengeluaran(id) {
    if (!confirm('Apakah Anda yakin ingin membatalkan pengeluaran ini?')) {
        return;
    }

    showLoading();
    
    fetch(`{{ route("kasir.pengeluaran.destroy", "") }}/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            showAlert('success', data.message);
            loadData(); // Reload data
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('error', 'Terjadi kesalahan');
        console.error('Error:', error);
    });
}

function showLaporanModal() {
    setupLaporanModal();
    $('#laporanModal').modal('show');
}

function setupLaporanModal() {
    // Setup kategori filter
    const kategoriSelect = document.getElementById('filter_kategori');
    kategoriSelect.innerHTML = '<option value="">Semua Kategori</option>';
    
    for (const [key, value] of Object.entries(kategoriList)) {
        kategoriSelect.innerHTML += `<option value="${key}">${value}</option>`;
    }
    
    loadLaporan();
}

function loadLaporan() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const kategori = document.getElementById('filter_kategori').value;
    
    showLoading();
    
    fetch(`{{ route("kasir.pengeluaran.laporan") }}?start_date=${startDate}&end_date=${endDate}&kategori=${kategori}`)
        .then(response => response.json())
        .then(data => {
            hideLoading();
            
            if (data.success) {
                renderLaporan(data);
            } else {
                document.getElementById('laporan-content').innerHTML = `
                    <div class="alert alert-danger">${data.message}</div>
                `;
            }
        })
        .catch(error => {
            hideLoading();
            document.getElementById('laporan-content').innerHTML = `
                <div class="alert alert-danger">Terjadi kesalahan saat memuat laporan</div>
            `;
            console.error('Error:', error);
        });
}

function renderLaporan(data) {
    const content = document.getElementById('laporan-content');
    
    let html = `
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Pengeluaran</h5>
                        <h3 class="card-text">${formatRupiah(data.total_pengeluaran)}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Jumlah Transaksi</h5>
                        <h3 class="card-text">${data.pengeluaran.length}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Rata-rata per Transaksi</h5>
                        <h3 class="card-text">${formatRupiah(data.total_pengeluaran / Math.max(data.pengeluaran.length, 1))}</h3>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Kategori Summary
    if (data.kategori_summary.length > 0) {
        html += `
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-chart-pie mr-2"></i>Ringkasan per Kategori</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th>Jumlah Transaksi</th>
                                    <th>Total</th>
                                    <th>Persentase</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.kategori_summary.map(item => `
                                    <tr>
                                        <td>${item.kategori}</td>
                                        <td>${item.count}</td>
                                        <td class="font-weight-bold">${formatRupiah(item.total)}</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: ${item.persentase}%" 
                                                     aria-valuenow="${item.persentase}" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                    ${item.persentase}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
    }

    // Detail Pengeluaran
    html += `
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-list mr-2"></i>Detail Pengeluaran</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Kategori</th>
                                <th>Keterangan</th>
                                <th>Jumlah</th>
                                <th>Dibuat Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.pengeluaran.map(item => `
                                <tr>
                                    <td>${formatTanggal(item.tanggal)}</td>
                                    <td><span class="badge badge-info">${item.kategori}</span></td>
                                    <td>${item.keterangan}</td>
                                    <td class="font-weight-bold text-danger">${formatRupiah(item.jumlah)}</td>
                                    <td>${item.user?.nama_lengkap || 'System'}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;

    content.innerHTML = html;
}

// Utility Functions
function formatRupiah(amount) {
    return 'Rp ' + parseInt(amount).toLocaleString('id-ID');
}

function formatTanggal(tanggal) {
    return new Date(tanggal).toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function formatWaktu(datetime) {
    return new Date(datetime).toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

function showAlert(type, message, container = null) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    if (container) {
        document.getElementById(container).innerHTML = alertHtml;
    } else {
        // Show as toast notification
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(toast);
        new bootstrap.Toast(toast).show();
        setTimeout(() => toast.remove(), 5000);
    }
}

function showLoading() {
    // Implement loading indicator
    console.log('Loading...');
}

function hideLoading() {
    // Hide loading indicator
    console.log('Loading complete');
}

function refreshData() {
    loadData();
}
</script>
@endpush