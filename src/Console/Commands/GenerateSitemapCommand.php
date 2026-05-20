<?php

namespace Otas\Sitemap\Console\Commands;

use Illuminate\Console\Command;
use Otas\Sitemap\Helpers\GenerateSitemapHelper;

class GenerateSitemapCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap file for the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!GenerateSitemapHelper::generate()) {
            $this->components->error('Failed to generate sitemap.');
            return;
        }

        $this->components->info('Sitemap has been created successfully.');
    }
}
