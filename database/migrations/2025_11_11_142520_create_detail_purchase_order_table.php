<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_purchase_order', function (Blueprint $table) {
            $table->bigIncrements('detail_po_id');
            $table->unsignedBigInteger('po_id');
            $table->unsignedBigInteger('produk_id')->nullable();
            $table->integer('jumlah_pesan');
            $table->decimal('harga_satuan', 10, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();

            $table->foreign('po_id')
                  ->references('po_id')
                  ->on('purchase_order')
                  ->onDelete('cascade');
            
            $table->foreign('produk_id')
                  ->references('produk_id')
                  ->on('produk')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_purchase_order');
    }
};