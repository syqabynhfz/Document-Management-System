<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // <-- Pastikan ini ada

class Template extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi secara massal (mass assignable).
     * Lebih aman daripada $guarded = [].
     */
    protected $fillable = [
        'admin_id',
        'name',
        'header_html',
        'footer_html',
    ];

    /**
     * Relasi ke Admin (Template ini dibuat oleh siapa).
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Relasi BARU: Template ini digunakan oleh banyak Dokumen.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}