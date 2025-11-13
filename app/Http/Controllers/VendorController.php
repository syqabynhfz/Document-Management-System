<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class VendorController extends Controller
{
    /**
     * Menampilkan halaman daftar vendor (Master To)
     */
    public function index(): Response
    {
        return Inertia::render('Data/Index', [
            // PERBAIKAN: Kita tambahkan 'withCount' untuk menghitung "TO"
            'vendors' => Vendor::withCount('documents') // <-- 'documents' adalah nama relasi di Model Vendor
                ->latest()
                ->get()
                ->map(fn ($vendor) => [
                    'id' => $vendor->id,
                    'name' => $vendor->name,
                    'address' => $vendor->address,
                    'contact_person' => $vendor->contact_person,
                    // 'documents_count' adalah nama kolom default dari withCount
                    'to_count' => $vendor->documents_count, 
                ]),
            'flash' => [
                'success' => session('success'),
            ],
        ]);
    }

    /**
     * Menampilkan halaman untuk membuat vendor baru.
     */
    public function create(): Response
    {
        return Inertia::render('Data/Create');
    }

    /**
     * Menyimpan vendor baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:vendors',
            'address' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
        ]);

        $request->user()->vendors()->create($validated);

        return redirect()->route('data.index')->with('success', 'Vendor berhasil dibuat.');
    }

    /**
     * Menampilkan halaman untuk mengedit vendor.
     */
    public function edit(Vendor $data): Response
    {
        return Inertia::render('Data/Edit', [
            'vendor' => $data
        ]);
    }

    /**
     * Memperbarui data vendor.
     */
    public function update(Request $request, Vendor $data): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('vendors')->ignore($data->id),
            ],
            'address' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
        ]);

        $data->update($validated);

        return redirect()->route('data.index')->with('success', 'Vendor berhasil diperbarui.');
    }

    /**
     * Menghapus vendor dari database.
     */
    public function destroy(Vendor $data): RedirectResponse
    {
        // PERBAIKAN: Cek relasi sebelum menghapus
        $documentCount = $data->documents()->count();

        if ($documentCount > 0) {
            // Jika ada dokumen, JANGAN HAPUS. Kirim pesan error.
            return redirect()->route('data.index')->with('error', 'Gagal! Vendor ini sedang digunakan oleh ' . $documentCount . ' dokumen.');
        }

        // Jika aman, baru hapus
        $data->delete();

        return redirect()->route('data.index')->with('success', 'Vendor berhasil dihapus.');
    }
}