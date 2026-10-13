<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    private const WRITE_INTERVAL_SECONDS = 45;

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->is_active) {
            $now = now();
            $lastActivity = $user->last_activity_at;

            if (! $lastActivity || $lastActivity->lte($now->copy()->subSeconds(self::WRITE_INTERVAL_SECONDS))) {
                DB::table('users')->where('id', $user->getAuthIdentifier())->update([
                    'last_activity_at' => $now,
                ]);
                $user->setAttribute('last_activity_at', $now);
            }
        }

        return $next($request);
    }
}
