<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('laporan_manajerial', function (Blueprint $table) {
            $table->id('laporan_id');
            
            // Informasi dasar
            $table->string('judul_laporan');
            $table->enum('jenis_laporan', ['harian', 'mingguan', 'bulanan', 'tahunan', 'special']);
            $table->date('periode_awal');
            $table->date('periode_akhir');
            
            // Data laporan (JSON)
            $table->json('data_laporan');
            
            // Ringkasan dan analisis
            $table->text('ringkasan')->nullable();
            $table->text('rekomendasi')->nullable();
            
            // Status dan approval
            $table->enum('status_laporan', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->text('catatan_admin')->nullable();
            
            // User relationships
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            // Soft deletes
            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index(['jenis_laporan', 'status_laporan']);
            $table->index(['periode_awal', 'periode_akhir']);
            $table->index('created_by');
            $table->index('approved_by');
        });
    }

    public function down()
    {
        Schema::dropIfExists('laporan_manajerial');
    }
};