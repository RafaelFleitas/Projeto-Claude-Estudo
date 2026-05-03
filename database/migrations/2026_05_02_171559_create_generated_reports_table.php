<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('generated_by')->constrained('users')->restrictOnDelete();
            $table->string('module');
            $table->string('format');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->unsignedBigInteger('file_size_bytes')->nullable();
            $table->json('filters')->nullable();
            $table->string('status')->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            $table->index('generated_by');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_reports');
    }
};
