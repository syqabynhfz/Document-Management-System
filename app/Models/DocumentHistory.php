<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute; // 1. Import Attribute

class DocumentHistory extends Model
{
    use HasFactory;

    protected $table = 'document_histories';
    protected $guarded = [];

    /**
     * 2. Definisikan relasi ke model Template.
     */
    public function template()
    {
        // Satu riwayat dimiliki oleh satu template
        return $this->belongsTo(Template::class);
    }

    /**
     * 3. Definisikan relasi ke model Admin.
     */
    public function admin()
    {
        // Satu riwayat dibuat oleh satu admin
        return $this->belongsTo(Admin::class);
    }

    /**
     * 4. Buat Accessor untuk kolom 'name' (yang tidak ada di database).
     * Ini akan membuat 'history.name' bisa diakses di frontend.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                // Decode JSON data_input
                $data = json_decode($attributes['data_input'], true);

                // Coba cari nama klien atau nama perusahaan dari data input
                if (!empty($data['nama_klien'])) {
                    return $this->template->name . ' - ' . $data['nama_klien'];
                }
                if (!empty($data['nama_perusahaan'])) {
                    return $this->template->name . ' - ' . $data['nama_perusahaan'];
                }

                // Jika tidak ada, gunakan nama template saja
                return $this->template->name ?? 'Nama Dokumen Tidak Ditemukan';
            }
        );
    }
}