<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Get the redirect path based on user role.
     * NOTE: This method might not be used by default Laravel auth scaffolding (like Breeze/Jetstream).
     * Ensure your login logic actually calls this method if you need role-based redirection.
     * The default redirection usually just uses the HOME constant.
     */
    public static function redirectTo(Request $request)
    {
        // If you specifically need different dashboards for admin/non-admin,
        // uncomment and adjust this logic. Otherwise, everyone goes to HOME.
        // if ($request->user() && $request->user()->is_admin) { // Assuming 'is_admin' boolean field
        //     return '/admin/dashboard'; // Or whatever the admin-specific dashboard route is
        // }

        return static::HOME; // Redirect everyone to the main dashboard
    }
}
