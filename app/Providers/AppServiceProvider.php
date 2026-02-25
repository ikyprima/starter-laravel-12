<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($url = config('app.url')) {
            \Illuminate\Support\Facades\URL::forceRootUrl($url);
        }

        if(config('app.env') === 'production' && config('app.force_https', true)) {
             $this->app['request']->server->set('HTTPS','on');
             \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Event::listen(
            \SocialiteProviders\Manager\SocialiteWasCalled::class,
            [\SocialiteProviders\Keycloak\KeycloakExtendSocialite::class, 'handle']
        );
    }
}
