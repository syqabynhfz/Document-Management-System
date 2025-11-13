<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('admin')->onDelete('cascade');
            $table->text('content_before');
            $table->text('content_after');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_revisions');
    }
};