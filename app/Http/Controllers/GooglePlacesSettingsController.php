<?php

namespace App\Http\Controllers;

use App\Exceptions\GooglePlacesException;
use App\Models\WorkspaceSetting;
use App\Services\GooglePlacesClient;
use App\Services\GooglePlacesUsageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GooglePlacesSettingsController extends Controller
{
    public function update(Request $request): RedirectResponse|JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        $validator = Validator::make($request->all(), [
            'api_key' => ['required', 'string', 'min:20', 'max:255', 'not_regex:/\s/'],
        ]);
        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'The supplied Google Places API key is invalid.',
                    'errors' => $validator->errors(),
                ], 422)->header('Cache-Control', 'no-store, private, max-age=0');
            }

            // Do not flash the API key into the session if a no-JavaScript submission fails validation.
            return back()->withErrors($validator);
        }
        $data = $validator->validated();

        WorkspaceSetting::putSecret('google_places_api_key', trim($data['api_key']));
        WorkspaceSetting::query()->where('setting_key', 'google_places_key_verified_at')->delete();
        $message = 'Google Places API key saved encrypted. Test the connection before searching.';

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'configured' => true,
                'key_hint' => '••••'.substr($data['api_key'], -4),
                'verified_at' => null,
            ])->header('Cache-Control', 'no-store, private, max-age=0')->header('Pragma', 'no-cache');
        }

        return back()->with('success', $message);
    }

    public function test(Request $request, GooglePlacesClient $client, GooglePlacesUsageService $usage): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        try {
            $client->verifyConnection();
        } catch (GooglePlacesException $exception) {
            return response()->json(['message' => $exception->getMessage()], $exception->httpStatus)
                ->header('Cache-Control', 'no-store, private, max-age=0');
        }

        $verifiedAt = now();
        WorkspaceSetting::putSecret('google_places_key_verified_at', $verifiedAt->toIso8601String());

        return response()->json([
            'message' => 'Connection verified. Geocoding and Places API (New) both responded successfully.',
            'verified_at' => $verifiedAt->format('d M Y, g:i A'),
            'usage' => $usage->overview(),
        ])->header('Cache-Control', 'no-store, private, max-age=0');
    }

    public function destroy(Request $request): RedirectResponse|JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        WorkspaceSetting::query()
            ->whereIn('setting_key', ['google_places_api_key', 'google_places_key_verified_at'])
            ->delete();
        $message = 'Google Places has been disconnected. Saved Place IDs and prospect ownership remain in GymXBook.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $message, 'configured' => false])
                ->header('Cache-Control', 'no-store, private, max-age=0');
        }

        return back()->with('success', $message);
    }
}
