<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->bigIncrements('transaksi_id');
            $table->string('no_transaksi', 50)->unique();
            $table->unsignedBigInteger('kasir_id')->nullable();
            $table->dateTime('tanggal_transaksi');
            $table->integer('total_item');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('diskon', 10, 2)->default(0);
            $table->decimal('total_bayar', 12, 2);
            $table->enum('metode_pembayaran', ['Tunai', 'Debit', 'QRIS', 'Transfer']);
            $table->decimal('jumlah_uang', 12, 2);
            $table->decimal('kembalian', 12, 2)->default(0);
            $table->enum('status', ['Selesai', 'Dibatalkan'])->default('Selesai');
            $table->timestamps();

            $table->foreign('kasir_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};