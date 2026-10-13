<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role', 24)->default('sales')->index();
            $table->boolean('is_active')->default(true)->index();
            $table->string('phone', 30)->nullable();
            $table->string('timezone', 64)->default('Asia/Kolkata');
            $table->boolean('notifications_enabled')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex('users_role_index');
            $table->dropIndex('users_is_active_index');
            $table->dropColumn([
                'role',
                'is_active',
                'phone',
                'timezone',
                'notifications_enabled',
            ]);
        });
    }
};
