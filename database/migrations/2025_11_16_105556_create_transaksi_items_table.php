<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksi_items', function (Blueprint $table) {
            $table->bigIncrements('transaksi_item_id');
            $table->unsignedBigInteger('transaksi_id');
            $table->unsignedBigInteger('produk_id');
            $table->string('barcode', 50);
            $table->string('nama_produk', 100);
            $table->integer('qty');
            $table->decimal('harga_jual', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();

            // Foreign keys
            $table->foreign('transaksi_id')
                  ->references('transaksi_id')
                  ->on('transaksi')
                  ->onDelete('cascade');

            $table->foreign('produk_id')
                  ->references('produk_id')
                  ->on('produk')
                  ->onDelete('restrict');

            // Indexes
            $table->index('transaksi_id');
            $table->index('produk_id');
            $table->index('barcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_items');
    }
};