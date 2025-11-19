<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus kolom username jika ada
            if (Schema::hasColumn('users', 'username')) {
                $table->dropColumn('username');
            }

            // Pastikan kolom email wajib dan unik
            $table->string('email', 100)->nullable(false)->change();

        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->unique()->after('user_id');
            $table->string('email', 100)->unique()->nullable()->change();
        });
    }
};
