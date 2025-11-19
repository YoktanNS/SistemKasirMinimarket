<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaporanManajerial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArsipController extends Controller
{
    public function index(Request $request)
    {
        $query = LaporanManajerial::with('user')->orderBy('created_at', 'desc');
        
        // Filter pencarian
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('judul_laporan', 'like', '%' . $request->search . '%')
                  ->orWhere('jenis_laporan', 'like', '%' . $request->search . '%')
                  ->orWhere('ringkasan', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->has('jenis') && $request->jenis) {
            $query->where('jenis_laporan', $request->jenis);
        }
        
        if ($request->has('start_date') && $request->start_date) {
            $query->where('periode_awal', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date) {
            $query->where('periode_akhir', '<=', $request->end_date);
        }

        $dokumen = $query->paginate(10);

        return view('admin.arsip.index', [
            'dokumen' => $dokumen
        ]);
    }

    public function create(Request $request)
    {
        // Tambahkan currentTab dengan default value
        return view('admin.arsip.upload', [
            'currentTab' => $request->get('tab', 'all')
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file_dokumen' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240', // 10MB
            'jenis_laporan' => 'required|string|max:100',
            'periode_awal' => 'required|date',
            'periode_akhir' => 'required|date|after_or_equal:periode_awal',
            'ringkasan' => 'nullable|string|max:500',
        ]);

        // Handle file upload
        if ($request->hasFile('file_dokumen')) {
            $file = $request->file('file_dokumen');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('dokumen', $fileName, 'public');
            
            // Simpan path file yang benar untuk diakses public
            $publicFilePath = 'storage/dokumen/' . $fileName;
        } else {
            return redirect()->back()->with('error', 'File dokumen harus diupload.');
        }

        // Generate judul laporan otomatis
        $judulLaporan = 'Laporan ' . $request->jenis_laporan . ' - ' . 
                       \Carbon\Carbon::parse($request->periode_awal)->format('d M Y') . ' s/d ' .
                       \Carbon\Carbon::parse($request->periode_akhir)->format('d M Y');

        // Buat record di database
        LaporanManajerial::create([
            'judul_laporan' => $judulLaporan,
            'jenis_laporan' => $request->jenis_laporan,
            'periode_awal' => $request->periode_awal,
            'periode_akhir' => $request->periode_akhir,
            'data_laporan' => json_encode([
                'file_path' => $publicFilePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_type' => $file->getMimeType(),
            ]),
            'ringkasan' => $request->ringkasan,
            'created_by' => Auth::id(),
        ]);

        $redirectTab = $request->get('redirect_tab', 'all');

        return redirect()->route('admin.arsip.index', ['tab' => $redirectTab])
            ->with('success', 'Dokumen berhasil diupload dan diarsipkan!');
    }

    /**
     * Method untuk melihat file
     */
    public function showFile($id)
    {
        try {
            $laporan = LaporanManajerial::findOrFail($id);
            
            $dataLaporan = is_array($laporan->data_laporan) 
                ? $laporan->data_laporan 
                : json_decode($laporan->data_laporan, true);

            $filePath = $dataLaporan['file_path'] ?? null;

            if (!$filePath || !file_exists(public_path($filePath))) {
                return redirect()->back()->with('error', 'File tidak ditemukan.');
            }

            // Tentukan content type berdasarkan file type
            $fileType = $dataLaporan['file_type'] ?? mime_content_type(public_path($filePath));
            
            return response()->file(public_path($filePath), [
                'Content-Type' => $fileType,
                'Content-Disposition' => 'inline; filename="' . ($dataLaporan['file_name'] ?? 'document') . '"'
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuka file: ' . $e->getMessage());
        }
    }

    /**
     * Method untuk download file
     */
    public function downloadFile($id)
    {
        try {
            $laporan = LaporanManajerial::findOrFail($id);
            
            $dataLaporan = is_array($laporan->data_laporan) 
                ? $laporan->data_laporan 
                : json_decode($laporan->data_laporan, true);

            $filePath = $dataLaporan['file_path'] ?? null;
            $fileName = $dataLaporan['file_name'] ?? 'document';

            if (!$filePath || !file_exists(public_path($filePath))) {
                return redirect()->back()->with('error', 'File tidak ditemukan.');
            }

            return response()->download(public_path($filePath), $fileName);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mendownload file: ' . $e->getMessage());
        }
    }

    /**
     * Hapus dokumen
     */
    public function destroy($id)
    {
        try {
            $laporan = LaporanManajerial::findOrFail($id);
            
            // Hapus file fisik
            $dataLaporan = is_array($laporan->data_laporan) 
                ? $laporan->data_laporan 
                : json_decode($laporan->data_laporan, true);
                
            $filePath = $dataLaporan['file_path'] ?? null;
            if ($filePath && file_exists(public_path($filePath))) {
                unlink(public_path($filePath));
            }
            
            $laporan->delete();

            return redirect()->route('admin.arsip.index')
                ->with('success', 'Dokumen berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus dokumen: ' . $e->getMessage());
        }
    }
}