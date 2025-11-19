<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentInfoToStokTransactions extends Migration
{
    public function up()
    {
        Schema::table('stok', function (Blueprint $table) {
            // Cek dulu apakah field sudah ada
            if (!Schema::hasColumn('stok', 'harga_beli')) {
                $table->decimal('harga_beli', 12, 2)->nullable()->after('jumlah');
            }
            
            if (!Schema::hasColumn('stok', 'total_pembayaran')) {
                $table->decimal('total_pembayaran', 12, 2)->nullable()->after('harga_beli');
            }
            
            if (!Schema::hasColumn('stok', 'status_pembayaran')) {
                $table->enum('status_pembayaran', ['lunas', 'hutang'])->default('lunas')->after('total_pembayaran');
            }
        });
    }

    public function down()
    {
        Schema::table('stok', function (Blueprint $table) {
            $table->dropColumn(['harga_beli', 'total_pembayaran', 'status_pembayaran']);
        });
    }
}