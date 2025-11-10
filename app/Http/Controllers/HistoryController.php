<?php

namespace App\Http\Controllers;

use App\Models\DocumentHistory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HistoryController extends Controller
{
    /**
     * Menampilkan halaman riwayat dokumen.
     */
    public function index()
    {
        $documentHistories = DocumentHistory::with(['template', 'admin'])
                                ->latest() // Mengurutkan berdasarkan created_at
                                ->get();

        // Tampilkan komponen Vue 'History/Index' dan kirim data sebagai props
        return Inertia::render('History/Index', [
            'documentHistories' => $documentHistories
        ]);
    }

    public function destroy(DocumentHistory $history)
    {
        $history->delete();

        return redirect()->route('history.index')->with('success', 'Riwayat berhasil dihapus!');
    }
}