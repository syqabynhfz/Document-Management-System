<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocType extends Model
{
    use HasFactory;
    
    // Memberitahu Eloquent untuk menggunakan tabel 'doc_types'
    protected $table = 'doc_types'; 

    protected $fillable = [
        'admin_id',
        'type_name',
        'product',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'doctype_id');
    }
}