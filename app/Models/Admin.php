<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany; // <-- 1. TAMBAHKAN IMPORT INI

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Nama tabel yang digunakan.
     */
    protected $table = 'admin';

    /**
     * 2. PERUBAHAN: Lebih baik menggunakan $fillable daripada $guarded = []
     * Pastikan kolom ini sesuai dengan tabel 'admin' Anda.
     */
    protected $fillable = [
        'full_name', // Sesuai file .sql Anda, kolomnya adalah 'full_name'
        'email',
        'password',
    ];

    /**
     * Atribut yang disembunyikan.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atribut yang di-cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // --- 3. TAMBAHKAN SEMUA RELASI BARU DI BAWAH INI ---

    /**
     * Relasi ke Templates (Admin memiliki banyak Template)
     */
    public function templates(): HasMany
    {
        return $this->hasMany(Template::class);
    }

    /**
     * Relasi ke Vendors (Admin memiliki banyak Vendor)
     */
    public function vendors(): HasMany
    {
        return $this->hasMany(Vendor::class);
    }

    /**
     * Relasi ke DocTypes (Admin memiliki banyak Tipe Dokumen)
     */
    public function docTypes(): HasMany
    {
        return $this->hasMany(DocType::class);
    }

    /**
     * Relasi ke Documents (Admin memiliki banyak Dokumen)
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Relasi ke Revisions (Admin memiliki banyak Revisi)
     */
    public function documentRevisions(): HasMany
    {
        return $this->hasMany(DocumentRevision::class);
    }
}