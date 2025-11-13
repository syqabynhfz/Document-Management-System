<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage; // <-- Pastikan ini ada

class TemplateController extends Controller
{
    /**
     * Menampilkan halaman daftar template.
     * (Menu: Templates)
     */
    public function index(): Response
    {
        return Inertia::render('Templates/Index', [
            'templates' => Template::latest()->get()->map(fn ($template) => [
                'id' => $template->id,
                'name' => $template->name,
                'created_at' => $template->created_at->format('d M Y'),
            ]),
            'flash' => [ // Menambahkan 'flash' untuk notifikasi
                'success' => session('success'),
            ],
        ]);
    }

    /**
     * Menampilkan halaman untuk membuat template baru.
     */
    public function create(): Response
    {
        return Inertia::render('Templates/Create');
    }

    /**
     * Menyimpan template baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:templates',
            'header_html' => 'nullable|string',
            'footer_html' => 'nullable|string',
        ]);

        // Menggunakan relasi untuk otomatis mengisi admin_id
        $request->user()->templates()->create($validated);

        return redirect()->route('templates.index')->with('success', 'Template berhasil dibuat.');
    }

    /**
     * Menampilkan halaman untuk mengedit template.
     */
    public function edit(Template $template): Response
    {
        return Inertia::render('Templates/Edit', [
            'template' => $template
        ]);
    }

    /**
     * Memperbarui template yang ada di database.
     */
    public function update(Request $request, Template $template): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('templates')->ignore($template->id),
            ],
            'header_html' => 'nullable|string',
            'footer_html' => 'nullable|string',
        ]);

        $template->update($validated);

        return redirect()->route('templates.index')->with('success', 'Template berhasil diperbarui.');
    }

    public function destroy(Template $template): RedirectResponse
    {
        // PERBAIKAN: Cek relasi sebelum menghapus
        // Kita hitung berapa dokumen yang menggunakan template_id ini
        $documentCount = $template->documents()->count();

        if ($documentCount > 0) {
            // Jika ada dokumen (hitungan > 0), JANGAN HAPUS
            // Kirim pesan error kembali ke halaman Index
            return redirect()->route('templates.index')->with('error', 'Gagal! Template ini sedang digunakan oleh ' . $documentCount . ' dokumen.');
        }

        // Jika hitungan = 0, aman untuk dihapus
        $template->delete();

        return redirect()->route('templates.index')->with('success', 'Template berhasil dihapus.');
    }

    /**
     * Fungsi untuk menangani upload gambar dari TinyMCE.
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $path = $request->file('file')->store('uploads/templates', 'public');
        $url = Storage::url($path);

        return response()->json([
            'location' => $url
        ]);
    }
} // <-- INI ADALAH '}' YANG HILANG