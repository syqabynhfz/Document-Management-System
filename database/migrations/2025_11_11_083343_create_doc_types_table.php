<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doc_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('admin')->onDelete('cascade');
            $table->string('type_name'); // e.g., "Surat Penawaran"
            $table->string('product')->nullable(); // e.g., "Jasa IT"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doc_types');
    }
};