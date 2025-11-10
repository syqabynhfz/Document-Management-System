<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\DocumentHistory; // Pastikan ini di-import
use Illuminate\Http\Request;
use Inertia\Inertia; // Pastikan ini di-import
use Barryvdh\DomPDF\Facade\Pdf; // Import DomPDF
use Illuminate\Support\Facades\Auth; // Import Auth

class GeneratorController extends Controller
{
    /**
     * Menampilkan halaman formulir dinamis berdasarkan template yang dipilih.
     */
    public function show(Template $template)
    {
        // Load relasi custom fields (pastikan relasi 'customFields' ada di model Template)
        $template->load('customFields');


        // Render komponen Vue 'FormulirGenerator' (sesuai klarifikasi Anda)
        return Inertia::render('FormulirGenerator', [
            'template' => $template,
            'customFields' => $template->customFields->sortBy('sort_order') // Urutkan fields
        ]);
    }

    /**
     * Memproses data form dan menghasilkan file PDF.
     */
    public function generate(Request $request, Template $template)
    {
        // 1. Validasi Input berdasarkan Custom Fields
        $rules = [];
        $template->load('customFields'); // Pastikan customFields ter-load
        foreach ($template->customFields as $field) {
            if ($field->is_required) {
                // Tambahkan 'required' jika field wajib diisi
                $rules[$field->field_name] = 'required';
            } else {
                $rules[$field->field_name] = 'nullable';
            }
            // Anda bisa menambahkan validasi tipe data di sini jika perlu
            // Contoh: if ($field->field_type === 'number') $rules[$field->field_name] .= '|numeric';
        }
        $validatedData = $request->validate($rules);

        // 2. Ambil HTML template
        $htmlContent = $template->body_html;
        // (Anda bisa menambahkan logika untuk header_html dan footer_html di sini)

        // 3. Ganti Placeholder dengan Data Validasi
        foreach ($validatedData as $key => $value) {
            // Gunakan e() untuk basic escaping agar aman dari XSS
            $htmlContent = str_replace('{{' . $key . '}}', e($value ?? ''), $htmlContent);
        }
        // Hapus placeholder yang mungkin tidak terisi (agar tidak muncul {{...}} di PDF)
        $htmlContent = preg_replace('/\{\{.*?\}\}/', '', $htmlContent);

        // 4. Generate PDF menggunakan DomPDF
        $pdf = Pdf::loadHTML($htmlContent);
        // Opsi: Atur ukuran kertas & orientasi jika perlu
        // $pdf->setPaper('A4', 'portrait');

        // 5. Simpan Riwayat ke document_histories
        DocumentHistory::create([
            'template_id' => $template->id,
            'admin_id' => Auth::id(), // ID admin yang sedang login
            'data_input' => json_encode($validatedData), // Simpan data yang sudah divalidasi
            // Kolom created_at & updated_at akan diisi otomatis
        ]);

        // 6. Return PDF sebagai file download
        // Membuat nama file yang unik dan deskriptif
        $fileName = \Illuminate\Support\Str::slug($template->name) . '_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }
}