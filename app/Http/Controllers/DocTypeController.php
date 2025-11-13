<?php

namespace App\Http\Controllers;

use App\Models\DocType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class DocTypeController extends Controller
{
    /**
     * Menampilkan halaman daftar tipe dokumen (Master Doc).
     */
    public function index(): Response
    {
        return Inertia::render('MasterDoc/Index', [
            // Kita tambahkan 'withCount' untuk menghitung "TO"
            'docTypes' => DocType::withCount('documents') // 'documents' = nama relasi di Model DocType
                ->latest()
                ->get()
                ->map(fn ($docType) => [
                    'id' => $docType->id,
                    'type_name' => $docType->type_name,
                    'product' => $docType->product,
                    // 'documents_count' adalah nama kolom default dari withCount
                    'to_count' => $docType->documents_count,
                ]),
            'flash' => [
                'success' => session('success'),
            ],
        ]);
    }

    /**
     * Menampilkan halaman untuk membuat tipe dokumen baru.
     */
    public function create(): Response
    {
        return Inertia::render('MasterDoc/Create');
    }

    /**
     * Menyimpan tipe dokumen baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type_name' => 'required|string|max:255|unique:doc_types',
            'product' => 'nullable|string|max:255',
        ]);

        $request->user()->docTypes()->create($validated); // Asumsi relasi 'docTypes()' di model Admin

        return redirect()->route('master-doc.index')->with('success', 'Tipe Dokumen berhasil dibuat.');
    }

    /**
     * Menampilkan halaman untuk mengedit tipe dokumen.
     */
    public function edit(DocType $master_doc): Response // Nama variabel {master_doc} dari rute
    {
        return Inertia::render('MasterDoc/Edit', [
            'docType' => $master_doc
        ]);
    }

    /**
     * Memperbarui data tipe dokumen.
     */
    public function update(Request $request, DocType $master_doc): RedirectResponse
    {
        $validated = $request->validate([
            'type_name' => [
                'required', 'string', 'max:255',
                Rule::unique('doc_types')->ignore($master_doc->id),
            ],
            'product' => 'nullable|string|max:255',
        ]);

        $master_doc->update($validated);

        return redirect()->route('master-doc.index')->with('success', 'Tipe Dokumen berhasil diperbarui.');
    }

    /**
     * Menghapus tipe dokumen dari database.
     */
    public function destroy(DocType $master_doc): RedirectResponse
    {
        // PERBAIKAN: Cek relasi sebelum menghapus
        $documentCount = $master_doc->documents()->count();

        if ($documentCount > 0) {
            // Jika ada dokumen, JANGAN HAPUS. Kirim pesan error.
            return redirect()->route('master-doc.index')->with('error', 'Gagal! Tipe Dokumen ini sedang digunakan oleh ' . $documentCount . ' dokumen.');
        }

        // Jika aman, baru hapus
        $master_doc->delete();

        return redirect()->route('master-doc.index')->with('success', 'Tipe Dokumen berhasil dihapus.');
    }
}