<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\GooglePlacesController;
use App\Http\Controllers\GooglePlacesSettingsController;
use App\Http\Controllers\LeadActivityController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadImportController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Middleware\TrackUserActivity;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:6,1')->name('login.store');
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->middleware('throttle:3,1')->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'active', TrackUserActivity::class])->group(function (): void {
    Route::post('/presence/ping', [PresenceController::class, 'ping'])->name('presence.ping');
    Route::get('/', DashboardController::class)->name('dashboard');

    // Specific lead routes are deliberately registered before the {lead} binding.
    Route::get('/leads/export', [LeadController::class, 'export'])->name('leads.export');
    Route::delete('/leads/bulk', [LeadController::class, 'bulkDestroy'])->name('leads.bulk-destroy');
    Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
    Route::get('/leads/create', [LeadController::class, 'create'])->name('leads.create');
    Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');
    Route::post('/leads/{lead}/activities', [LeadActivityController::class, 'store'])->name('leads.activities.store');
    Route::patch('/leads/{lead}/status', [LeadController::class, 'updateStatus'])->name('leads.status');
    Route::get('/leads/{lead}/edit', [LeadController::class, 'edit'])->name('leads.edit');
    Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
    Route::put('/leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
    Route::delete('/leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');

    Route::get('/follow-ups', [FollowUpController::class, 'index'])->name('follow-ups.index');
    Route::get('/follow-ups/create', [FollowUpController::class, 'create'])->name('follow-ups.create');
    Route::post('/follow-ups', [FollowUpController::class, 'store'])->name('follow-ups.store');
    Route::patch('/follow-ups/{followUp}/complete', [FollowUpController::class, 'complete'])->name('follow-ups.complete');
    Route::delete('/follow-ups/{followUp}', [FollowUpController::class, 'destroy'])->name('follow-ups.destroy');

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/imports', [LeadImportController::class, 'index'])->name('imports.index');
    Route::post('/imports', [LeadImportController::class, 'upload'])->name('imports.upload');
    Route::get('/imports/{leadImport}', [LeadImportController::class, 'show'])->name('imports.show');
    Route::post('/imports/{leadImport}/mapping', [LeadImportController::class, 'saveMapping'])->name('imports.mapping');
    Route::post('/imports/{leadImport}/validate', [LeadImportController::class, 'validateImport'])->name('imports.validate');
    Route::post('/imports/{leadImport}/commit', [LeadImportController::class, 'commit'])->name('imports.commit');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
    Route::put('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications');
    Route::middleware('role:admin')->group(function (): void {
        Route::put('/settings/integrations/google-places', [GooglePlacesSettingsController::class, 'update'])->name('settings.google-places.update');
        Route::post('/settings/integrations/google-places/test', [GooglePlacesSettingsController::class, 'test'])->middleware('throttle:3,1')->name('settings.google-places.test');
        Route::delete('/settings/integrations/google-places', [GooglePlacesSettingsController::class, 'destroy'])->name('settings.google-places.destroy');
    });
    Route::get('/prospecting', [GooglePlacesController::class, 'index'])->name('prospecting.index');
    Route::get('/prospecting/{prospect}/details', [GooglePlacesController::class, 'details'])->middleware('throttle:20,1')->name('prospecting.details');
    Route::get('/prospecting/{prospect}/convert-form', [GooglePlacesController::class, 'conversionForm'])->name('prospecting.convert-form');
    Route::patch('/prospecting/{prospect}/status', [GooglePlacesController::class, 'updateStatus'])->middleware('throttle:30,1')->name('prospecting.status');
    Route::patch('/prospecting/{prospect}/assignment', [GooglePlacesController::class, 'reassign'])->middleware('role:admin,manager')->name('prospecting.reassign');
    Route::middleware('role:admin,manager')->group(function (): void {
        Route::post('/prospecting/search', [GooglePlacesController::class, 'search'])->middleware('throttle:8,1')->name('prospecting.search');
        Route::post('/prospecting/assign', [GooglePlacesController::class, 'assign'])->middleware('throttle:10,1')->name('prospecting.assign');
    });
    Route::middleware('role:admin,manager')->group(function (): void {
        Route::get('/settings/users/presence', [TeamMemberController::class, 'presence'])->name('settings.users.presence');
        Route::get('/settings/users', [TeamMemberController::class, 'index'])->name('settings.users');
        Route::post('/settings/users', [TeamMemberController::class, 'store'])->name('settings.users.store');
        Route::patch('/settings/users/{user}', [TeamMemberController::class, 'update'])->name('settings.users.update');
        Route::delete('/settings/users/{user}', [TeamMemberController::class, 'destroy'])->name('settings.users.destroy');
    });
});
