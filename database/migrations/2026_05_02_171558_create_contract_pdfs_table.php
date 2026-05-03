<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_pdfs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->foreignId('generated_by')->constrained('users')->restrictOnDelete();
            $table->uuid('validation_code')->unique();
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedBigInteger('file_size_bytes')->nullable();
            $table->timestamp('generated_at');
            $table->timestamps();

            $table->index('validation_code');
            $table->index('contract_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_pdfs');
    }
};
