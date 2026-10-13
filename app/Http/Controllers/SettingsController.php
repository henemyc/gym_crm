<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkspaceSetting;
use App\Services\GooglePlacesUsageService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(Request $request): View
    {
        $team = $request->user()->canManageTeam()
            ? User::query()->orderByRaw("CASE role WHEN 'admin' THEN 0 WHEN 'manager' THEN 1 ELSE 2 END")->orderBy('name')->get()
            : collect([$request->user()]);

        $placesIntegration = null;
        $placesUsage = null;
        if ($request->user()->isAdmin()) {
            $apiKey = WorkspaceSetting::secret('google_places_api_key');
            $verifiedAt = WorkspaceSetting::secret('google_places_key_verified_at');
            $placesIntegration = [
                'configured' => $apiKey !== null,
                'key_hint' => $apiKey ? '••••'.substr($apiKey, -4) : null,
                'verified_at' => $verifiedAt ? CarbonImmutable::parse($verifiedAt)->timezone($request->user()->timezone ?: config('app.timezone')) : null,
            ];
            $placesUsage = app(GooglePlacesUsageService::class)->overview();
        }

        return view('settings.index', compact('team', 'placesIntegration', 'placesUsage'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+().\-\s]{7,30}$/'],
            'timezone' => ['required', 'timezone'],
        ]);
        $user->update($data);

        return back()->with('success', 'Your profile has been updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(10)->letters()->numbers()],
        ]);
        $request->user()->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', 'Your password has been changed.');
    }

    public function updateNotifications(Request $request): RedirectResponse
    {
        $request->validate(['notifications_enabled' => ['nullable', 'boolean']]);
        $request->user()->update(['notifications_enabled' => $request->boolean('notifications_enabled')]);

        return back()->with('success', 'Notification preferences saved.');
    }
}
