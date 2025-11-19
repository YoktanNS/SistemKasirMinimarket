<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function index()
    {
        // ✅ FIX: Hapus withCount purchaseOrders
        $suppliers = Supplier::orderBy('nama_supplier')
            ->paginate(10);

        return view('admin.supplier.index', compact('suppliers'));
    }

    public function show($id)
    {
        // ✅ FIX: Hapus with purchaseOrders
        $supplier = Supplier::findOrFail($id);
        return view('admin.supplier.show', compact('supplier'));
    }

    public function create()
    {
        return view('admin.supplier.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_supplier' => 'required|string|max:20|unique:supplier,kode_supplier',
            'nama_supplier' => 'required|string|max:100',
            'alamat' => 'nullable|string',
            'no_telepon' => 'required|string|max:15',
            'email' => 'nullable|email|max:100',
            'kontak_person' => 'nullable|string|max:100',
            'status' => 'in:Aktif,Nonaktif',
        ]);

        Supplier::create([
            'kode_supplier' => $request->kode_supplier,
            'nama_supplier' => $request->nama_supplier,
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telepon,
            'email' => $request->email,
            'kontak_person' => $request->kontak_person,
            'status' => $request->status ?? 'Aktif',
            // ✅ FIX: Hapus user_id (kolom sudah dihapus dari database)
        ]);

        return redirect()->route('admin.supplier.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('admin.supplier.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        
        $request->validate([
            'kode_supplier' => 'required|string|max:20|unique:supplier,kode_supplier,' . $id . ',supplier_id',
            'nama_supplier' => 'required|string|max:100',
            'alamat' => 'nullable|string',
            'no_telepon' => 'required|string|max:15',
            'email' => 'nullable|email|max:100',
            'kontak_person' => 'nullable|string|max:100',
            'status' => 'in:Aktif,Nonaktif',
        ]);

        $supplier->update([
            'kode_supplier' => $request->kode_supplier,
            'nama_supplier' => $request->nama_supplier,
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telepon,
            'email' => $request->email,
            'kontak_person' => $request->kontak_person,
            'status' => $request->status ?? 'Aktif',
        ]);

        return redirect()->route('admin.supplier.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return back()->with('success', 'Supplier berhasil dihapus.');
    }
}