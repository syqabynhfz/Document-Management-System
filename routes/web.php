<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

// 1. IMPORT CONTROLLER YANG AKAN KITA GUNAKAN
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\DocTypeController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\HistoryController;

// 2. IMPORT MODEL UNTUK DATA DASHBOARD
use App\Models\Document;
use App\Models\Template;
use App\Models\Vendor;
use App\Models\DocType;
use Illuminate\Support\Facades\Auth; // <-- Import Auth

/*
|--------------------------------------------------------------------------
| Web Routes (ARSITEKTUR BARU)
|--------------------------------------------------------------------------
*/

// Rute root, alihkan ke login atau dashboard
Route::get('/', function () {
    if (auth()->check()) {
        return Redirect::route('dashboard');
    }
    return Redirect::route('login');
});

// Grup Rute yang memerlukan Login (Middleware Auth)
Route::middleware(['auth', 'verified'])->group(function () {
    
    // ===================================================================
    // MENU: DASHBOARD
    // ===================================================================
    Route::get('/dashboard', function () {
        // Logika baru untuk Dashboard
        // Menghitung jumlah dokumen untuk setiap tipe dokumen
        $masterDocCounts = DocType::withCount('documents')->get(); 
        // Menghitung jumlah dokumen untuk setiap vendor
        $masterToCounts = Vendor::withCount('documents')->get();

        return Inertia::render('Dashboard', [
            'totalDocuments' => Document::count(),
            'totalTemplates' => Template::count(),
            'masterDocData' => $masterDocCounts, // Data untuk grafik Master Doc
            'masterToData' => $masterToCounts,   // Data untuk grafik Master To
        ]);
    })->name('dashboard');

    // ===================================================================
    // MENU: DATA (VENDORS / MASTER TO)
    // ===================================================================
    // Route::resource() otomatis membuat route untuk:
    // index, create, store, edit, update, destroy
    Route::resource('/data', VendorController::class)->names('data');

    // ===================================================================
    // MENU: TEMPLATES (HEADER/FOOTER)
    // ===================================================================
    Route::resource('/templates', TemplateController::class)->names('templates');
    Route::post('/templates/upload-image', [TemplateController::class, 'uploadImage'])
        ->name('templates.upload_image');

    // ===================================================================
    // MENU: MASTER DOC (DOC TYPES)
    // ===================================================================
    Route::resource('/master-doc', DocTypeController::class)->names('master-doc');

    // ===================================================================
    // MENU: DOCUMENT (PEMBUATAN MANUAL)
    // ===================================================================
    Route::resource('/document', DocumentController::class)->names('document');

    Route::get('/document/{document}/download', [DocumentController::class, 'download'])
        ->name('document.download');

    // ===================================================================
    // MENU: HISTORY (REVISIONS)
    // ===================================================================
    // Menampilkan daftar dokumen yang punya revisi
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    // Menampilkan detail revisi untuk satu dokumen
    Route::get('/history/{document}', [HistoryController::class, 'show'])->name('history.show');


    // ===================================================================
    // Rute Profil Bawaan
    // ===================================================================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

// Load file auth.php (untuk login, register, dll)
require __DIR__.'/auth.php';