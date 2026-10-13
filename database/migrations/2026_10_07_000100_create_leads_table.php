<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('gym_name');
            $table->string('phone', 32)->index();
            $table->string('location', 120)->nullable()->index();
            $table->string('source', 80)->default('Other')->index();
            $table->string('priority', 16)->default('warm')->index();
            $table->string('status', 24)->default('new')->index();
            $table->longText('notes')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'priority']);
            $table->index(['assigned_to', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
