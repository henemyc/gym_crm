<?php

namespace App\Providers;

use App\Models\Lead;
use App\Policies\LeadPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\FollowUp;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Load global CRM helpers even if Composer's optimized files autoload is stale.
        require_once dirname(__DIR__).'/Support/helpers.php';
    }

    public function boot(): void
    {
        Gate::policy(Lead::class, LeadPolicy::class);

        View::composer('layouts.partials.sidebar', function ($view): void {
            $user = auth()->user();
            if (! $user) {
                return;
            }

            $view->with([
                'sidebarLeadCount' => Lead::query()->visibleTo($user)->count(),
                'sidebarDueCount' => FollowUp::query()->visibleTo($user)->open()->whereDate('due_at', now($user->timezone ?: config('app.timezone'))->toDateString())->count(),
            ]);
        });
        View::composer('layouts.partials.topbar', function ($view): void {
            $user = auth()->user();
            if (! $user) {
                return;
            }

            $view->with('sidebarDueCount', FollowUp::query()->visibleTo($user)->open()->whereDate('due_at', now($user->timezone ?: config('app.timezone'))->toDateString())->count());
        });
    }
}
