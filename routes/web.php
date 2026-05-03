<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ContractPdfController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\ValidationController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

// Public contract validation — no auth required
Route::get('validate/{code}', [ValidationController::class, 'show'])->name('contract.validate');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', \App\Http\Controllers\DashboardController::class)->name('dashboard');

    // Contracts
    Route::resource('contracts', ContractController::class);
    Route::post('contracts/{contract}/pdfs', [ContractPdfController::class, 'store'])->name('contract-pdfs.store');
    Route::get('contracts/pdfs/{contractPdf}/download', [ContractPdfController::class, 'download'])->name('contract-pdfs.download');
    Route::delete('contracts/pdfs/{contractPdf}', [ContractPdfController::class, 'destroy'])->name('contract-pdfs.destroy');

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
