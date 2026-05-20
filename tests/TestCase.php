<?php

namespace Otas\Sitemap\Tests;

use Otas\Sitemap\SitemapServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
  public function setUp(): void
  {
    parent::setUp();
    // additional setup
  }

  protected function getPackageProviders($app)
  {
    return [
        SitemapServiceProvider::class,
    ];
  }

  protected function getEnvironmentSetUp($app)
  {
    // perform environment setup
  }
}
