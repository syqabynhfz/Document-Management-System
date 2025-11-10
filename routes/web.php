<?php

use App\Http\Controllers\AiTemplateGeneratorController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\GeneratorController;
use App\Models\DocumentHistory;
use App\Models\Template;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Redirect::route('login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', [
        'userName'        => Auth::user()->full_name, 
        'totalDocuments'  => DocumentHistory::count(), 
        'totalTemplates'  => Template::count(),
        'recentDocuments' => DocumentHistory::latest()->take(5)->get(), 
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/templates', [TemplateController::class, 'index']) 
    ->middleware(['auth'])
    ->name('templates.index');

Route::get('/templates/create', [TemplateController::class, 'create'])->name('templates.create');

Route::get('/history', [HistoryController::class, 'index'])
    ->middleware(['auth'])
    ->name('history.index');

Route::get('/history/{history}/download', [HistoryController::class, 'download'])
    ->middleware(['auth'])
    ->name('history.download');

Route::delete('/history/{history}', [HistoryController::class, 'destroy'])
    ->middleware(['auth'])
    ->name('history.destroy');

Route::get('/generator', function () {
    return Inertia::render('FormulirGenerator');
})->middleware(['auth'])->name('generator.index');

Route::get('/generator/{template}', [GeneratorController::class, 'show'])
    ->middleware(['auth'])
    ->name('generator.show');
    
Route::get('/generator/{template}/generate', [GeneratorController::class, 'generate']) 
    ->middleware(['auth'])
    ->name('generator.generate');

Route::get('/ai-importer', function () {
    return Inertia::render('AI/Importer'); 
})->middleware(['auth'])->name('ai.importer.show');

Route::post('/ai-generate-template', [AiTemplateGeneratorController::class, 'process'])
    ->middleware(['auth'])
    ->name('ai.generate.template');

Route::post('/templates', [TemplateController::class, 'store'])
    ->name('templates.store');

Route::post('/templates/preview-pdf', [TemplateController::class, 'previewPdf'])
    ->middleware(['auth'])
    ->name('templates.preview');
    
Route::delete('/templates/{template}', [TemplateController::class, 'destroy'])
    ->middleware(['auth'])
    ->name('templates.destroy');

Route::post('/templates/upload-image', [TemplateController::class, 'uploadImage'])
    ->middleware(['auth'])
    ->name('templates.upload_image');

require __DIR__.'/auth.php';
