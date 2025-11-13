<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    use HasFactory;

    // 'body_content' dihapus dari $fillable
    protected $fillable = [
        'title',
        'admin_id',
        'template_id',
        'vendor_id',
        'doctype_id',
    ];

    // Relasi "milik siapa"
    public function admin(): BelongsTo { return $this->belongsTo(Admin::class); }
    public function template(): BelongsTo { return $this->belongsTo(Template::class); }
    public function vendor(): BelongsTo { return $this->belongsTo(Vendor::class); }
    public function docType(): BelongsTo { return $this->belongsTo(DocType::class, 'doctype_id'); }

    // Relasi "memiliki banyak"
    public function revisions(): HasMany { return $this->hasMany(DocumentRevision::class); }

    // --- RELASI BARU UNTUK HALAMAN ---
    public function pages(): HasMany
    {
        return $this->hasMany(DocumentPage::class)->orderBy('page_number');
    }
}