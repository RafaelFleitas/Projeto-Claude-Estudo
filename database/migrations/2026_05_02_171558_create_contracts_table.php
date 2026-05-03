<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table): void {
            $table->id();
            $table->string('contrato');
            $table->string('numero_relatorio')->nullable();
            $table->string('projeto')->nullable();
            $table->string('task_azure')->nullable();
            $table->string('nota_fiscal')->nullable();
            $table->decimal('valor_total', 15, 2)->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('user_id');
            $table->index('contrato');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
