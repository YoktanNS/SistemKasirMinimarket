<?php
namespace App\Http\Controllers\Api; // <-- INI YANG DIPERBAIKI

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Untuk tanggal

class TransactionController extends Controller 
{
    public function store(Request $request) 
    {
        $validated = $request->validate([
            'total_amount' => 'required|numeric',
            'payment_method' => 'required|string',
            'jumlah_bayar' => 'required|numeric',
            'cart' => 'required|array',
            'cart.*.produk_id' => 'required|integer|exists:produk,produk_id',
            'cart.*.quantity' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();
            
            $total_item = 0;
            $subtotal_cart = 0;

            foreach ($validated['cart'] as $item) {
                $total_item += $item['quantity'];
                $product = Product::find($item['produk_id']);
                $subtotal_cart += $product->harga_jual * $item['quantity'];
            }

            // 1. Buat header transaksi (sesuai tabel 'transaksi' Anda)
            $transaction = Transaction::create([
                'no_transaksi' => 'TRX-' . time(), // Buat No. Transaksi unik
                'kasir_id' => null, // Anda bisa tambahkan ID kasir jika sudah ada login
                'tanggal_transaksi' => Carbon::now(),
                'total_item' => $total_item,
                'subtotal' => $subtotal_cart,
                'diskon' => 0, // Anda bisa tambahkan logika diskon nanti
                'total_bayar' => $validated['total_amount'],
                'metode_pembayaran' => $validated['payment_method'],
                'jumlah_uang' => $validated['jumlah_bayar'],
                'kembalian' => $validated['jumlah_bayar'] - $validated['total_amount'],
            ]);

            // 2. Loop & buat detail transaksi (sesuai tabel 'detail_transaksi')
            foreach ($validated['cart'] as $item) {
                $product = Product::find($item['produk_id']);
                
                // Cek stok
                if ($product->stok_tersedia < $item['quantity']) {
                    throw new \Exception('Stok tidak mencukupi untuk ' . $product->nama_produk);
                }

                TransactionDetail::create([
                    'transaksi_id' => $transaction->transaksi_id,
                    'produk_id' => $product->produk_id,
                    'nama_produk' => $product->nama_produk,
                    'harga_satuan' => $product->harga_jual,
                    'jumlah' => $item['quantity'],
                    'subtotal' => $product->harga_jual * $item['quantity'],
                ]);

                // 3. Kurangi stok produk (menggunakan kolom 'stok_tersedia')
                $product->decrement('stok_tersedia', $item['quantity']);
            }

            DB::commit();
            return response()->json(['message' => 'Transaksi berhasil disimpan!', 'transaction_id' => $transaction->transaksi_id], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}