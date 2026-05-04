<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AiAnalysisController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\ContractAttachmentController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ContractPdfController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\ValidationController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

// Public contract validation — no auth required
Route::get('validate/{code}', [ValidationController::class, 'show'])->name('contract.validate');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    // Contracts
    Route::resource('contracts', ContractController::class);
    Route::post('contracts/{contract}/pdfs', [ContractPdfController::class, 'store'])->name('contract-pdfs.store');
    Route::get('contracts/pdfs/{contractPdf}/download', [ContractPdfController::class, 'download'])->name('contract-pdfs.download');
    Route::delete('contracts/pdfs/{contractPdf}', [ContractPdfController::class, 'destroy'])->name('contract-pdfs.destroy');
    Route::post('contracts/{contract}/attachments', [ContractAttachmentController::class, 'store'])->name('contract-attachments.store');
    Route::get('contracts/attachments/{contractAttachment}/download', [ContractAttachmentController::class, 'download'])->name('contract-attachments.download');
    Route::delete('contracts/attachments/{contractAttachment}', [ContractAttachmentController::class, 'destroy'])->name('contract-attachments.destroy');

    // AI Analysis — limited to 20 requests/hour per user, 100/day globally
    Route::middleware('throttle:20,60')->group(function () {
        Route::post('contracts/pdfs/{contractPdf}/analyze', [AiAnalysisController::class, 'analyzeContractPdf'])->name('ai.analyze.contract-pdf');
        Route::post('contracts/attachments/{contractAttachment}/analyze', [AiAnalysisController::class, 'analyzeAttachment'])->name('ai.analyze.attachment');
        Route::post('reports/{report}/analyze', [AiAnalysisController::class, 'analyzeReport'])->name('ai.analyze.report');
    });

    // Audit log
    Route::get('audits', [AuditController::class, 'index'])->name('audits.index');

    // Reports
    Route::resource('reports', ReportController::class)->except(['edit', 'update']);
    Route::get('reports/{report}/download', [ReportController::class, 'download'])->name('reports.download');
    Route::post('reports/{report}/telegram', [TelegramController::class, 'send'])->name('reports.telegram');

    // Admin — user management
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });
});

require __DIR__.'/settings.php';
