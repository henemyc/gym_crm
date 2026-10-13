<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_places_api_usage', function (Blueprint $table): void {
            $table->id();
            $table->string('billing_month', 7);
            $table->string('sku', 48);
            $table->unsignedInteger('reserved_requests')->default(0);
            $table->timestamps();
            $table->unique(['billing_month', 'sku']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_places_api_usage');
    }
};
