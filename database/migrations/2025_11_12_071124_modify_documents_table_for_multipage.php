<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('body_content'); // Hapus kolom body yang lama
        });
    }
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->text('body_content')->after('title'); // Kembalikan jika di-rollback
        });
    }
};