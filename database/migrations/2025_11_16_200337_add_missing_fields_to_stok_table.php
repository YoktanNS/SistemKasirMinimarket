<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingFieldsToStokTable extends Migration
{
    public function up()
    {
        Schema::table('stok', function (Blueprint $table) {
            // Cek dulu apakah field sudah ada
            if (!Schema::hasColumn('stok', 'total_pembayaran')) {
                $table->decimal('total_pembayaran', 12, 2)->default(0)->after('harga_beli_satuan');
            }
            
            if (!Schema::hasColumn('stok', 'status_pembayaran')) {
                $table->enum('status_pembayaran', ['lunas', 'hutang'])->default('lunas')->after('total_pembayaran');
            }
        });
    }

    public function down()
    {
        Schema::table('stok', function (Blueprint $table) {
            $table->dropColumn(['total_pembayaran', 'status_pembayaran']);
        });
    }
}