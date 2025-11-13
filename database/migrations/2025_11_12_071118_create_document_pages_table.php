<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->integer('page_number');
            $table->mediumText('content'); // Konten HTML untuk halaman ini
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('document_pages');
    }
};