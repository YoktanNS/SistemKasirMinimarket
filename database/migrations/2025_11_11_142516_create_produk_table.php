<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->bigIncrements('produk_id');
            $table->string('barcode', 50)->unique();
            $table->string('nama_produk', 100);
            $table->unsignedBigInteger('kategori_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->decimal('harga_beli', 10, 2);
            $table->decimal('harga_jual', 10, 2);
            $table->integer('stok_tersedia')->default(0);
            $table->integer('stok_minimum')->default(5);
            $table->string('satuan', 20);
            $table->string('gambar_produk', 255)->nullable();
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['Tersedia', 'Habis', 'Nonaktif'])->default('Tersedia');
            $table->timestamps();

            $table->foreign('kategori_id')
                  ->references('kategori_id')
                  ->on('kategori_produk')
                  ->onDelete('set null');
            
            $table->foreign('supplier_id')
                  ->references('supplier_id')
                  ->on('supplier')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};