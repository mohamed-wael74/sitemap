<?php

namespace Otas\Sitemap\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SitemapPublishRoutesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:publish-routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish the routes for the Sitemap package';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $routesPublishSubDirectory = config('sitemap.routes_publish_subdirectory');

        if (File::exists(base_path("routes/{$routesPublishSubDirectory}sitemap_website_routes.php"))) {
            $this->components->warn("Routes have already been published in routes/{$routesPublishSubDirectory} directory.");
            return Command::SUCCESS;
        }

        if (! $this->shouldPublishAlreadyLoadedRoutes()) {
            $this->components->warn('Routes publishing is aborted.');
            return Command::SUCCESS;
        }

        File::copy(
            __DIR__ . "/../../routes/sitemap_website_routes.php",
            base_path("routes/{$routesPublishSubDirectory}sitemap_website_routes.php")
        );

        $this->callSilent('vendor:publish', [
            '--provider' => 'Otas\Sitemap\SitemapServiceProvider',
        ]);

        exec('composer dump-autoload');

        $this->components->info('Routes have been published successfully.');

        return Command::SUCCESS;
    }

    private function shouldPublishAlreadyLoadedRoutes(): bool
    {
        if (config('sitemap.load_routes')) {
            return $this->confirm(
                'Loading routes is enabled in the configuration file. Do you want to publish them anyway?',
                false
            );
        }
        return true;
    }
}