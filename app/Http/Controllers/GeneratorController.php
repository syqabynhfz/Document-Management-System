<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\DocumentHistory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GeneratorController extends Controller
{
    /**
     * Menampilkan halaman formulir dinamis berdasarkan template.
     */
    public function show(Template $template): Response
    {
        // Memuat relasi 'customFields' dari model Template
        // Saya asumsikan nama relasinya 'customFields' sesuai kode Anda
        $template->load('customFields');

        return Inertia::render('FormulirGenerator', [
            'template' => $template->only('id', 'name', 'body_html'),
            'customFields' => $template->customFields->sortBy('sort_order')
                ->map(function ($field) {
                    // Mengirimkan data field yang bersih ke frontend
                    return [
                        'id' => $field->id,
                        'field_label' => $field->field_label,
                        'field_name' => $field->field_name,
                        'field_type' => $field->field_type,
                        'is_required' => $field->is_required,
                    ];
                })
        ]);
    }

    /**
     * Memproses data form dan menghasilkan file PDF.
     */
    public function generate(Request $request, Template $template)
    {
        $template->load('customFields');
        $formData = $request->input('formData'); // Kita asumsikan data ada di 'formData'

        // 1. Ambil HTML template
        $htmlContent = $template->body_html;
        
        // 2. Ganti Placeholder Sederhana (Non-Tabel)
        foreach ($template->customFields as $field) {
            $key = $field->field_name; // e.g., "nama_klien"
            if ($field->field_type !== 'table' && isset($formData[$key])) {
                $value = htmlspecialchars($formData[$key] ?? '');
                $htmlContent = str_replace('{{' . $key . '}}', $value, $htmlContent);
            }
        }

        // 3. Ganti Placeholder Tabel (Logika Kompleks)
        foreach ($template->customFields as $field) {
            $key = $field->field_name;
            if ($field->field_type === 'table' && isset($formData[$key])) {
                // Panggil helper untuk membuat HTML tabel
                $tableHtml = $this->buildTableHtml($formData[$key]);
                $htmlContent = str_replace('{{' . $key . '}}', $tableHtml, $htmlContent);
            }
        }

        // 4. Hapus placeholder yang tersisa
        $htmlContent = preg_replace('/\{\{.*?\}\}/', '', $htmlContent);

        // 5. Simpan Riwayat
        DocumentHistory::create([
            'template_id' => $template->id,
            'admin_id' => Auth::id(),
            'data_input' => json_encode($formData),
        ]);

        // 6. Generate PDF dan kirim sebagai download
        $pdf = Pdf::loadHTML($htmlContent);
        $fileName = Str::slug($template->name) . '_' . now()->format('Ymd_His') . '.pdf';
        
        // return $pdf->stream($fileName); // Gunakan stream() untuk preview
        return $pdf->download($fileName); // Gunakan download() untuk langsung unduh
    }

    /**
     * Helper untuk mengubah array data tabel menjadi string HTML.
     * PENTING: Sesuaikan kolom (Uraian, Qty, Harga) dengan kebutuhan Anda!
     */
    private function buildTableHtml(array $items): string
    {
        // Sesuaikan header tabel ini
        $table = '<table border="1" style="width:100%; border-collapse: collapse; margin-top: 10px;">';
        $table .= '<thead>
                        <tr style="background-color: #f2f2f2;">
                            <th style="padding: 8px; text-align: left;">Uraian / Keterangan</th>
                            <th style="padding: 8px; text-align: center;">Qty</th>
                            <th style="padding: 8px; text-align: right;">Harga Satuan (Rp)</th>
                        </tr>
                   </thead>';
        $table .= '<tbody>';

        if (empty($items)) {
            $table .= '<tr><td colspan="3" style="padding: 8px; text-align: center;">- Tidak ada data -</td></tr>';
        } else {
            foreach ($items as $item) {
                // Sesuaikan key array ('uraian', 'qty', 'harga') ini
                $table .= '<tr>';
                $table .= '<td style="padding: 8px;">' . htmlspecialchars($item['uraian'] ?? '') . '</td>';
                $table .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($item['qty'] ?? '1') . '</td>';
                $table .= '<td style="padding: 8px; text-align: right;">' . number_format($item['harga'] ?? 0, 0, ',', '.') . '</td>';
                $table .= '</tr>';
            }
        }

        $table .= '</tbody></table>';
        return $table;
    }
}