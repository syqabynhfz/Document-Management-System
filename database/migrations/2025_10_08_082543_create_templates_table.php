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
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            
            // Definisikan kolom ini sebagai nullable (opsional)
            $table->text('header_html')->nullable();
            $table->mediumText('body_html');
            $table->text('footer_html')->nullable();
            
            // Definisikan foreign key
            $table->foreignId('admin_id')->nullable()->constrained('admin')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};