<?php

namespace Otas\Sitemap;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Otas\Sitemap\Console\Commands\GenerateSitemapCommand;
use Otas\Sitemap\Console\Commands\SitemapPublishRoutesCommand;
use Otas\Sitemap\Console\Commands\SitemapSetupCommand;

class SitemapServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        require_once __DIR__ . '/Helpers/SitemapHelperFunctions.php';

        $this->registerRoutes();

        if ($this->app->runningInConsole()) {

            $this->commands([
                SitemapSetupCommand::class,
                SitemapPublishRoutesCommand::class,
                GenerateSitemapCommand::class,
            ]);

            $this->publishConfig();
        }
    }

    /**
     * load routes from the route files.
     *
     * @return void
     */
    protected function registerRoutes(): void
    {
        if (config('sitemap.load_routes')) {
            Route::group($this->routeConfiguration(), function () {
                $this->loadRoutesFrom(__DIR__ . '/routes/sitemap_website_routes.php');
            });
        }
    }

    /**
     * Get the route configuration for the package.
     *
     * @return array
     */
    protected function routeConfiguration(): array
    {
        return [
            'prefix' => config('sitemap.package_routes_prefix'),
        ];
    }

    /**
     * Publish config files.
     */
    protected function publishConfig(): void
    {
        $this->publishes([
            __DIR__ . '/config/sitemap.php' => config_path('sitemap.php'),
        ], 'sitemap-config');
    }
}
