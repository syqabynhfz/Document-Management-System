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
       Schema::create('template_fields', function (Blueprint $table) {
        $table->id(); // Kolom ID Primary Key

        // Kolom Foreign Key ke tabel 'templates'
        $table->foreignId('template_id') 
              ->constrained('templates') // Merujuk ke tabel 'templates'
              ->onDelete('cascade'); // Jika template dihapus, field-nya ikut terhapus

        $table->string('field_name', 100); // Nama variabel (misal: nama_klien)
        $table->string('field_label', 150); // Label yang tampil di form (misal: Nama Klien)
        $table->string('field_type', 50); // Tipe input (text, textarea, date, number)
        $table->boolean('is_required')->default(true); // Apakah wajib diisi?
        $table->integer('sort_order')->default(0); // Urutan tampil di form
        $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_fields');
    }
};
