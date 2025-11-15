<?php

// PASTIKAN NAMESPACE INI BENAR
namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth; // <-- Pastikan ini ada

class HistoryController extends Controller
{
    /**
     * Menampilkan daftar dokumen yang memiliki riwayat revisi.
     */
    public function index(): Response
    {
        return Inertia::render('History/Index', [
            'documents' => Document::has('revisions') // <-- Hanya ambil dokumen yang punya revisi
                ->withCount('revisions') // <-- Ambil juga jumlah revisinya
                ->with(['vendor', 'docType']) // Ambil data relasi untuk tabel
                ->latest('updated_at')
                ->get()
                ->map(fn ($doc) => [
                    'id' => $doc->id,
                    'title' => $doc->title,
                    'vendor_name' => $doc->vendor->name ?? 'N/A',
                    'doctype_name' => $doc->docType->type_name ?? 'N/A',
                    'revisions_count' => $doc->revisions_count, // Jumlah revisi
                    'last_updated' => $doc->updated_at->format('d M Y, H:i'),
                ]),
            'flash' => [ // Tambahkan ini untuk notifikasi
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }

    /**
     * Menampilkan detail riwayat revisi untuk satu dokumen.
     */
    public function show(Document $document): Response
    {
        // Muat dokumen beserta semua revisinya, 
        // dan admin yang membuat revisi tersebut
        $document->load([
            'revisions' => fn ($query) => $query->latest(), // Urutkan revisi terbaru di atas
            'revisions.admin' // Ambil data admin pembuat revisi
        ]);

        return Inertia::render('History/Show', [
            'document' => $document
        ]);
    }
}