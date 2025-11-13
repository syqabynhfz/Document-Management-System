<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Template;
use App\Models\Vendor;
use App\Models\DocType;
use App\Models\DocumentPage;
use App\Models\DocumentRevision;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // <-- Penting untuk Transaksi
use Barryvdh\DomPDF\Facade\Pdf; // <-- Penting untuk Download

class DocumentController extends Controller
{
    /**
     * Menampilkan daftar dokumen.
     */
    public function index(): Response
    {
        return Inertia::render('Document/Index', [
            'documents' => Document::with(['vendor', 'docType'])
                ->latest()
                ->get()
                ->map(fn ($doc) => [
                    'id' => $doc->id,
                    'title' => $doc->title,
                    'vendor_name' => $doc->vendor->name ?? 'N/A',
                    'doctype_name' => $doc->docType->type_name ?? 'N/A',
                    'created_at' => $doc->created_at->format('d M Y'),
                ]),
            'flash' => [ 'success' => session('success') ],
        ]);
    }

    /**
     * Menampilkan halaman "persiapan" untuk membuat dokumen.
     */
    public function create(): Response
    {
        return Inertia::render('Document/Create', [
            'templates' => Template::all(['id', 'name']),
            'vendors' => Vendor::all(['id', 'name']),
            'docTypes' => DocType::all(['id', 'type_name']),
        ]);
    }

    /**
     * Menyimpan dokumen BARU (draf) dan redirect ke editor.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'template_id' => 'required|exists:templates,id',
            'vendor_id' => 'required|exists:vendors,id',
            'doctype_id' => 'required|exists:doc_types,id',
        ]);

        $document = DB::transaction(function () use ($request, $validated) {
            // 1. Buat dokumen utama
            $document = $request->user()->documents()->create($validated);

            // 2. Buat halaman kosong pertama
            $document->pages()->create([
                'page_number' => 1,
                'content' => '<p>Mulai ketik dokumen Anda di sini...</p>'
            ]);

            return $document;
        });

        // Alihkan ke halaman edit, bukan ke index
        return redirect()->route('document.edit', $document)->with('success', 'Draf dokumen berhasil dibuat. Silakan isi konten.');
    }

    /**
     * Menampilkan halaman editor multi-halaman (Edit.vue).
     */
    public function edit(Document $document): Response
    {
        // Kirim dokumen, relasi, DAN data master
        return Inertia::render('Document/Edit', [
            'document' => $document->load(['pages', 'template']), // Muat 'pages' dan 'template'
            'templates' => Template::all(['id', 'name']),
            'vendors' => Vendor::all(['id', 'name']),
            'docTypes' => DocType::all(['id', 'type_name']),
        ]);
    }

    /**
     * Memperbarui dokumen dan MENYIMPAN RIWAYAT REVISI.
     */
    public function update(Request $request, Document $document): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'template_id' => 'required|exists:templates,id',
            'vendor_id' => 'required|exists:vendors,id',
            'doctype_id' => 'required|exists:doc_types,id',
            'pages' => 'required|array|min:1', // Pastikan ada setidaknya 1 halaman
            'pages.*.id' => 'nullable|exists:document_pages,id',
            'pages.*.content' => 'required|string',
        ]);
        
        // Gabungkan semua konten halaman untuk log revisi
        $newFullContent = collect($validated['pages'])->pluck('content')->implode('<div style="page-break-after: always;"></div>');
        $oldFullContent = $document->pages->pluck('content')->implode('<div style="page-break-after: always;"></div>');

        DB::transaction(function () use ($request, $document, $validated, $oldFullContent, $newFullContent) {
            // 1. Update metadata dokumen utama
            $document->update($request->only('title', 'template_id', 'vendor_id', 'doctype_id'));

            // 2. Simpan Revisi jika konten berubah
            if ($oldFullContent !== $newFullContent) {
                DocumentRevision::create([
                    'document_id' => $document->id,
                    'admin_id' => Auth::id(),
                    'content_before' => $oldFullContent,
                    'content_after' => $newFullContent,
                ]);
            }

            // 3. Sinkronkan halaman (Update/Create/Delete halaman)
            $pageIdsToKeep = [];
            foreach ($validated['pages'] as $index => $pageData) {
                $page = $document->pages()->updateOrCreate(
                    [
                        'id' => $pageData['id'] ?? null, // Cari berdasarkan ID
                    ],
                    [
                        'page_number' => $index + 1, // Atur ulang nomor halaman
                        'content' => $pageData['content']
                    ]
                );
                $pageIdsToKeep[] = $page->id;
            }

            // Hapus halaman yang tidak lagi ada di data (misal: dihapus oleh user)
            $document->pages()->whereNotIn('id', $pageIdsToKeep)->delete();
        });

        return redirect()->back()->with('success', 'Dokumen berhasil diperbarui.');
    }

    /**
     * Menghapus dokumen.
     */
    public function destroy(Document $document): RedirectResponse
    {
        $document->delete(); // onDelete('cascade') akan menghapus pages & revisions
        return redirect()->route('document.index')->with('success', 'Dokumen berhasil dihapus.');
    }

    /**
     * --- FITUR DOWNLOAD PDF (VERSI FINAL - Menggunakan Blade View) ---
     */
    public function download(Document $document)
    {
        // 1. Muat data yang diperlukan
        $document->load(['template', 'pages']);

        // 2. Fungsi helper untuk memperbaiki path gambar
        $fixImagePaths = function ($html) {
            return preg_replace_callback(
                '/src="([^"]+)"/',
                function ($matches) {
                    $url = $matches[1];
                    if (str_contains($url, '/storage/')) {
                        $relativePath = ltrim(strstr($url, '/storage/'), '/');
                        $filePath = public_path($relativePath);
                        if (file_exists($filePath)) {
                            // dompdf butuh path absolut
                            return 'src="' . $filePath . '"';
                        }
                    }
                    // Jika URL eksternal (http://) atau file tidak ditemukan, biarkan
                    return $matches[0];
                },
                $html
            );
        };

        // 3. Siapkan data untuk dikirim ke Blade View
        $data = [
            'document' => $document,
            'header' => $fixImagePaths($document->template->header_html ?? ''),
            'footer' => $fixImagePaths($document->template->footer_html ?? ''),
            'pages' => $document->pages->map(function ($page) use ($fixImagePaths) {
                // Perbaiki path gambar di setiap halaman
                $page->content = $fixImagePaths($page->content);
                return $page;
            })
        ];

        // 4. Generate PDF menggunakan loadView
        $pdf = Pdf::loadView('documents.pdf', $data); // <-- Menggunakan file Blade
        $pdf->setPaper('A4', 'portrait');

        // 5. Kirim sebagai download
        return $pdf->download($document->title . '.pdf');
    }
}