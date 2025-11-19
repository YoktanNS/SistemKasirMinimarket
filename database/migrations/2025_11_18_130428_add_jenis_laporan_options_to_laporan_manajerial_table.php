<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Ubah kolom enum untuk menambah opsi baru
        DB::statement("ALTER TABLE laporan_manajerial MODIFY COLUMN jenis_laporan ENUM('Penjualan Harian','Penjualan Bulanan','Produk Terlaris','Profit Margin','Laporan Harian','Laporan Bulanan','Lainnya') NOT NULL");
    }

    public function down()
    {
        // Kembalikan ke enum semula
        DB::statement("ALTER TABLE laporan_manajerial MODIFY COLUMN jenis_laporan ENUM('Penjualan Harian','Penjualan Bulanan','Produk Terlaris','Profit Margin','Lainnya') NOT NULL");
    }
};