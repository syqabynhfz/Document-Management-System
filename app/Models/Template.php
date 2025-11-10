<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TemplateField; // <-- 1. Import model TemplateField

class Template extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Mendefinisikan relasi one-to-many ke TemplateField.
     * Satu Template bisa memiliki banyak Custom Fields.
     * Langsung diurutkan berdasarkan sort_order.
     */
    public function customFields() // <-- 2. Tambahkan method relasi ini
    {
        // Relasi hasMany ke model TemplateField
        // Secara otomatis Laravel akan mencari foreign key 'template_id'
        return $this->hasMany(TemplateField::class)->orderBy('sort_order'); 
    }

    /**
     * (Opsional tapi bagus) Mendefinisikan relasi belongsTo ke model Admin.
     * Satu Template dimiliki oleh satu Admin.
     */
    public function admin() // <-- Tambahkan juga relasi ke Admin jika perlu
    {
        return $this->belongsTo(Admin::class);
    }
}