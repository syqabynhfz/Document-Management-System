<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Template;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('document_histories', function (Blueprint $table) {
            $table->id();
            
            // Foreign key ke tabel templates
            $table->foreignId('template_id')->nullable()->constrained('templates')->onDelete('set null');
            
            // Foreign key ke tabel admin
            $table->foreignId('admin_id')->nullable()->constrained('admin')->onDelete('set null');
            
            $table->json('data_input')->nullable();
            $table->timestamps();
            
            // timestamps() akan membuat created_at dan updated_at
            // Jika Anda hanya butuh 'generated_at', baris di atas sudah cukup.
            // Jika butuh keduanya, gunakan $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_histories');
    }
};