<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_imports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('filename');
            $table->string('file_path');
            $table->string('status', 24)->default('uploaded')->index();
            $table->json('headers')->nullable();
            $table->json('mapping')->nullable();
            $table->json('summary')->nullable();
            $table->unsignedInteger('rows_total')->default(0);
            $table->unsignedInteger('rows_valid')->default(0);
            $table->unsignedInteger('rows_invalid')->default(0);
            $table->unsignedInteger('rows_imported')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_imports');
    }
};
