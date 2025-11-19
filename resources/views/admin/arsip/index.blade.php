@extends('layouts.app')

@section('title', 'Arsip Dokumen')
@section('page_title', '📁 Arsip Dokumen')

@section('content')
<div class="bg-gradient-to-b from-gray-50 to-white shadow-lg rounded-2xl p-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 tracking-wide">📁 Arsip Dokumen</h2>
            <p class="text-gray-500 text-sm mt-1">Kelola semua dokumen</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('admin.laporan.index') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition">
                Kembali ke Laporan
            </a>
            
            <a href="{{ route('admin.arsip.create') }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-md transition">
                Upload Dokumen
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
        <form method="GET" action="{{ route('admin.arsip.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Dokumen</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari berdasarkan judul..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                    Cari
                </button>
                <a href="{{ route('admin.arsip.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        {{ session('error') }}
    </div>
    @endif

    <!-- Tabel Dokumen -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul Dokumen</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">File</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat Oleh</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($dokumen as $item)
                    @php
                        $dataLaporan = is_array($item->data_laporan) 
                            ? $item->data_laporan 
                            : json_decode($item->data_laporan, true);
                        $hasFile = isset($dataLaporan['file_path']) && file_exists(public_path($dataLaporan['file_path']));
                        $fileName = $dataLaporan['file_name'] ?? 'File';
                        $fileType = $dataLaporan['file_type'] ?? 'unknown';
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $item->judul_laporan ?? $item->jenis_laporan }}</div>
                            @if($item->ringkasan)
                            <div class="text-xs text-gray-500 mt-1">{{ Str::limit($item->ringkasan, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $item->jenis_laporan }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($item->periode_awal)->format('d M Y') }} - 
                            {{ \Carbon\Carbon::parse($item->periode_akhir)->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3">
                            @if($hasFile)
                                <div class="text-xs">
                                    <div class="font-medium text-green-600">{{ $fileName }}</div>
                                    <div class="text-gray-500">{{ $fileType }}</div>
                                </div>
                            @else
                                <span class="text-xs text-red-500">No File</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-900">{{ $item->user->nama_lengkap ?? 'System' }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            {{ $item->created_at->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-center gap-1">
                                <!-- Tombol Lihat File -->
                                @if($hasFile)
                                <a href="{{ route('admin.arsip.file.view', $item->laporan_id) }}" 
                                   target="_blank"
                                   class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded text-xs font-medium transition shadow-sm">
                                    Lihat
                                </a>
                                
                                <!-- Tombol Download File -->
                                <a href="{{ route('admin.arsip.file.download', $item->laporan_id) }}" 
                                   class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-1.5 rounded text-xs font-medium transition shadow-sm">
                                    Download
                                </a>
                                @endif
                                
                                <!-- Tombol Hapus -->
                                <form action="{{ route('admin.laporan.destroy', $item->laporan_id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('Hapus dokumen ini?')"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded text-xs font-medium transition shadow-sm">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <span class="text-4xl mb-2">📁</span>
                                <p class="text-lg">Belum ada dokumen diarsipkan</p>
                                <a href="{{ route('admin.arsip.create') }}" class="text-blue-600 hover:text-blue-800 mt-2">
                                    Upload dokumen pertama Anda →
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($dokumen->count() > 0)
        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
            {{ $dokumen->links() }}
        </div>
        @endif
    </div>
</div>
@endsection