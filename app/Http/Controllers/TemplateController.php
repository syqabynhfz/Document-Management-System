<?php


namespace App\Http\Controllers; 

use App\Models\Template; 
use App\Models\TemplateField; 
use App\Models\DocumentHistory;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth; 
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class TemplateController extends Controller
{

    /**
     * Menampilkan halaman daftar template.
     */
    public function index()
    {
        $templates = Template::latest()->get();
        
        return Inertia::render('Templates/Index', [
            'templates' => $templates
        ]);
    }

    /**
     * Menampilkan halaman untuk membuat template baru.
     */
    public function create()
    {
        return Inertia::render('Templates/Create');
    }

    /**
     * Menyimpan template baru ke database.
     * Method ini akan dipanggil oleh route POST /templates
     */
    public function store(Request $request)
    {
        // 1. Validasi data utama template
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:templates', 
            'description' => 'nullable|string',
            'body_html' => 'required|string',
            'header_html' => 'nullable|string',
            'footer_html' => 'nullable|string',
            
            'custom_fields' => 'nullable|array',
            'custom_fields.*.field_name' => 'required_with:custom_fields|string|max:100',
            'custom_fields.*.field_label' => 'required_with:custom_fields|string|max:150',
            'custom_fields.*.field_type' => 'required_with:custom_fields|string|in:text,textarea,date,number',
        ]);

        $template = Template::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'] ?? null,
            'body_html' => $validatedData['body_html'],
            'header_html' => $validatedData['header_html'] ?? null,
            'footer_html' => $validatedData['footer_html'] ?? null,
            'admin_id' => Auth::id(), 
        ]);

        if (isset($validatedData['custom_fields']) && is_array($validatedData['custom_fields'])) {
            $sortOrder = 0;
            $fieldsToInsert = [];
            foreach ($validatedData['custom_fields'] as $fieldData) {
                if (isset($fieldData['field_name'], $fieldData['field_label'], $fieldData['field_type'])) {
                    $fieldsToInsert[] = [
                        'template_id' => $template->id, 
                        'field_name' => $fieldData['field_name'],
                        'field_label' => $fieldData['field_label'],
                        'field_type' => $fieldData['field_type'],
                        'is_required' => $fieldData['is_required'] ?? true, 
                        'sort_order' => $sortOrder++,
                    ];
                }
            }
            if (!empty($fieldsToInsert)) {
                TemplateField::insert($fieldsToInsert);
            }
        }

        return redirect()->route('templates.index')->with('success', 'Template berhasil disimpan!');
    }

    public function previewPdf(Request $request)
    {
        $request->validate([
            'body_html' => 'required|string',
        ]);

        $htmlContent = $request->input('body_html');

        try {
            $pdf = Pdf::loadHTML($htmlContent);
            return $pdf->download('template-preview.pdf');
        } catch (\Exception $e) {
            // Jika HTML-nya error (misal tag tidak ditutup), dompdf akan gagal
            return response()->json(['error' => 'Gagal membuat PDF: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Template $template)
    {
        
        $template->delete();
        
        return redirect()->route('templates.index')->with('success', 'Template berhasil dihapus!');
    }

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
}