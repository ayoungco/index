<?php

namespace App\Providers;

use App\Support\SiteSettings;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Auth0\Laravel\Events\AuthenticationFailed;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SiteSettings::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(SiteSettings $siteSettings): void
    {
        $settings = $siteSettings->all();

        config([
            'app.name' => $settings['site_name'],
        ]);

        View::share('siteSettings', $settings);

        // Log Auth0 callback failures and gracefully recover from Invalid state
        Event::listen(AuthenticationFailed::class, function (AuthenticationFailed $event): void {
            // Log the exception details for troubleshooting
            try {
                logger()->error('Auth0 AuthenticationFailed', [
                    'message' => $event->exception->getMessage(),
                    'type' => get_class($event->exception),
                ]);
            } catch (\Throwable) {
                // noop
            }

            // If the error is a state mismatch, suppress the exception so the SDK redirects to /login
            if (stripos($event->exception->getMessage(), 'Invalid state') !== false) {
                $event->throwException = false;
            }
        });
    }
}
