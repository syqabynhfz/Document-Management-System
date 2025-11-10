<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\IOFactory;
use Spatie\PdfToText\Pdf;
use Illuminate\Support\Facades\Http;

class AiTemplateGeneratorController extends Controller
{
    /**
     * Memproses input dokumen (file atau teks) dan mengembalikan
     * hasil analisis AI sebagai flash data.
     */
    public function process(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'document_file' => 'nullable|file|mimes:docx,pdf,txt|max:5120', // Maks 5MB
            'raw_text' => 'nullable|string|max:15000', // Batasi panjang teks
        ]);

        if (!$request->hasFile('document_file') && !$request->filled('raw_text')) {
             return redirect()->back()->withErrors(['input' => 'Mohon unggah file atau masukkan teks.']);
        }

        $extractedHtml = '';

        // 2. Ekstraksi Teks (Konversi ke HTML Sederhana)
        try {
            if ($request->hasFile('document_file')) {
                $file = $request->file('document_file');
                $extension = $file->getClientOriginalExtension();

                if ($extension === 'docx') {
                    $phpWord = IOFactory::load($file->getPathname());
                    $xmlWriter = IOFactory::createWriter($phpWord, 'HTML');
                    $extractedHtml = $xmlWriter->getContent();
                } elseif ($extension === 'pdf') {
                    $text = Pdf::getText($file->getPathname());
                    $extractedHtml = '<p>' . nl2br(e($text)) . '</p>'; 
                } elseif ($extension === 'txt') {
                    $text = file_get_contents($file->getPathname());

                    $extractedHtml = '<p>' . nl2br(e($text)) . '</p>';
                }
            } elseif ($request->filled('raw_text')) {
                $text = $request->input('raw_text');
                $extractedHtml = '<p>' . nl2br(e($text)) . '</p>';
            }
        } catch (\Exception $e) {
            Log::error('Gagal Ekstraksi File: ' . $e->getMessage());
            return redirect()->back()->withErrors(['input' => 'Gagal memproses file: ' . $e->getMessage()]);
        }

        if (empty($extractedHtml)) {
            return redirect()->back()->withErrors(['input' => 'Tidak ada konten yang bisa diekstrak dari file.']);
        }

        // 3. Persiapan Prompt AI
        $prompt = $this->buildAiPrompt($extractedHtml);

        // 4. Panggil API AI (Menggunakan HTTP Client)
        try {
            $apiKey = env('GEMINI_API_KEY');
            if (empty($apiKey)) {
                throw new \Exception('GEMINI_API_KEY belum diatur di .env');
            }
            
          $url = 'https://generativelanguage.googleapis.com/v1/models/gemini-1.0-pro:generateContent?key=' . $apiKey;

            // Siapkan body request sesuai format Gemini
            $requestBody = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ];

            // Kirim request POST menggunakan HTTP Client Laravel
            $response = Http::timeout(60) // Set timeout 60 detik
                            ->post($url, $requestBody);

            if (!$response->successful()) {
                Log::error('Gagal Panggil AI - Respons Gagal:', $response->json());
                throw new \Exception('Gagal menghubungi API Gemini: ' . $response->body());
            }

            $aiResponseJson = $response->json('candidates.0.content.parts.0.text');

            if (empty($aiResponseJson)) {
                Log::error('AI mengembalikan respons kosong atau struktur tidak dikenal:', $response->json());
                throw new \Exception('AI mengembalikan respons kosong.');
            }
            
            $aiResponseJson = preg_replace('/^```json\s*|\s*```\s*$/', '', $aiResponseJson);

        } catch (\Exception $e) {
            Log::error('Gagal Panggil AI (Exception): ' . $e->getMessage());
            return redirect()->back()->withErrors(['input' => 'Gagal menghubungi server AI: ' . $e->getMessage()]);
        }

        // 5. Parse dan Validasi Respons AI
        $processedData = json_decode($aiResponseJson, true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($processedData['body_html']) || !isset($processedData['custom_fields'])) {
            Log::error('AI mengembalikan JSON tidak valid:', ['response' => $aiResponseJson]);
            return redirect()->back()->withErrors(['input' => 'AI mengembalikan respons yang tidak valid. Coba lagi.']);
        }

        // 6. Kirim data ke Halaman Review
        session()->flash('processedData', $processedData);
        return redirect()->route('ai.importer.show');
    }

    /**
     * Membangun prompt yang detail untuk AI.
     */
    private function buildAiPrompt(string $htmlContent): string
    {
        // ... (Prompt Anda tetap sama) ...
        return <<<PROMPT
Anda adalah asisten ahli yang mengubah dokumen HTML statis menjadi template dinamis.
Tugas Anda adalah:
1.  Menganalisis dokumen HTML berikut.
2.  Mengidentifikasi semua teks yang terlihat seperti variabel (nama orang, nama perusahaan, tanggal, nomor surat, alamat, harga, item baris).
3.  Mengganti variabel-variabel tersebut dengan format placeholder Jinja2: `{{nama_variabel}}`.
4.  Nama variabel HARUS dalam format snake_case (huruf kecil dan garis bawah), misal: `nama_klien`, `tanggal_surat`.
5.  Membuat `field_label` yang rapi dan deskriptif (Contoh: "Nama Klien").
6.  Menebak `field_type` (pilihan: text, textarea, date, number).
7.  Mengembalikan HANYA sebuah objek JSON yang valid, tanpa teks penjelasan atau pembuka/penutup.

Format JSON yang wajib Anda kembalikan adalah:
{
  "body_html": "HTML lengkap dokumen yang sudah berisi placeholder...",
  "custom_fields": [
    { "field_name": "nama_variabel_1", "field_label": "Label Formulir 1", "field_type": "text" },
    { "field_name": "variabel_lain", "field_label": "Label Lain", "field_type": "date" }
  ]
}

Sekarang, proses dokumen HTML berikut:

$htmlContent
PROMPT;
    }
}