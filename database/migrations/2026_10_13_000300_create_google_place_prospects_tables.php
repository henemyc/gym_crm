<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_place_prospects', function (Blueprint $table): void {
            $table->id();
            $table->string('place_id', 255)->unique();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->unique()->constrained('leads')->nullOnDelete();
            $table->string('status', 24)->default('new')->index();
            $table->timestamps();
            $table->index(['assigned_to', 'status', 'created_at']);
        });

        Schema::create('google_place_prospect_activities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('prospect_id')->constrained('google_place_prospects')->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event', 32);
            $table->foreignId('from_assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('to_assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('from_status', 24)->nullable();
            $table->string('to_status', 24)->nullable();
            $table->timestamps();
            $table->index(['prospect_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_place_prospect_activities');
        Schema::dropIfExists('google_place_prospects');
    }
};
