<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body_content'); // Konten manual dari editor
            
            // Foreign Keys (Relasi)
            $table->foreignId('admin_id')->constrained('admin')->onDelete('cascade');
            $table->foreignId('template_id')->constrained('templates')->onDelete('restrict');
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('restrict');
            $table->foreignId('doctype_id')->constrained('doc_types')->onDelete('restrict');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};