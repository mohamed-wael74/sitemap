<?php

namespace Otas\Sitemap\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SitemapSetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install and Publish sitemap Package';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Publishing configuration...');

        if (! $this->configExists('sitemap.php')) {
            $this->publishConfiguration();
            $this->components->info('Published configuration');
        } else {
            if ($this->shouldOverwriteConfig()) {
                $this->components->info('Overwriting configuration file...');
                $this->publishConfiguration(true);
            } else {
                $this->components->info('Existing configuration is not overwritten');
            }
        }

        $this->components->info('Caching configs...');
        $this->call('config:cache');

        return Command::SUCCESS;
    }

    private function configExists($fileName)
    {
        return File::exists(config_path($fileName));
    }

    private function shouldOverwriteConfig()
    {
        return $this->confirm(
            'Config file already exists. Do you want to overwrite it?',
            false
        );
    }

    private function publishConfiguration($forcePublish = false)
    {
        $params = [
            '--provider' => 'Otas\Sitemap\SitemapServiceProvider',
        ];

        if ($forcePublish === true) {
            $params['--force'] = true;
        }

        $this->call('vendor:publish', $params);
    }
}