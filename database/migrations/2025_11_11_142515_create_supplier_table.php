<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier', function (Blueprint $table) {
            $table->bigIncrements('supplier_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('kode_supplier', 20)->unique();
            $table->string('nama_supplier', 100);
            $table->text('alamat')->nullable();
            $table->string('no_telepon', 15);
            $table->string('email', 100)->nullable();
            $table->string('kontak_person', 100)->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_pengiriman')->default(0);
            $table->decimal('ketepatan_waktu', 5, 2)->default(0);
            $table->decimal('kualitas_produk', 3, 2)->default(0);
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier');
    }
};