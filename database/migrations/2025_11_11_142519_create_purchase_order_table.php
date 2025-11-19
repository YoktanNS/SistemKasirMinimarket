<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_order', function (Blueprint $table) {
            $table->bigIncrements('po_id');
            $table->string('no_po', 50)->unique();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->date('tanggal_po');
            $table->date('tanggal_kirim_estimasi')->nullable();
            $table->date('tanggal_kirim_aktual')->nullable();
            $table->integer('total_item');
            $table->decimal('total_harga', 12, 2);
            $table->enum('status', ['Pending', 'Disetujui', 'Dikirim', 'Diterima', 'Dibatalkan'])->default('Pending');
            $table->text('catatan')->nullable();
            $table->text('konfirmasi_supplier')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('supplier_id')
                  ->references('supplier_id')
                  ->on('supplier')
                  ->onDelete('set null');
            
            $table->foreign('admin_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('set null');
            
            $table->foreign('approved_by')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order');
    }
};