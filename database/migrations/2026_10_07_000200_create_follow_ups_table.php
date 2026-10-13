<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follow_ups', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('kind', 20)->default('follow_up')->index();
            $table->dateTime('due_at')->index();
            $table->string('outcome', 32)->nullable();
            $table->text('remark')->nullable();
            $table->dateTime('completed_at')->nullable()->index();
            $table->dateTime('next_follow_up_at')->nullable()->index();
            $table->dateTime('meeting_at')->nullable()->index();
            $table->string('meeting_with', 120)->nullable();
            $table->dateTime('trial_at')->nullable()->index();
            $table->text('trial_details')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['completed_at', 'due_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
    }
};
