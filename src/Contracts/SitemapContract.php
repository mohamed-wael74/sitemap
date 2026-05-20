<?php

namespace Otas\Sitemap\Contracts;

interface SitemapContract
{
    public static function sitemapTranslatedSegments(): array;
    
    public static function buildSitemapUrls(): array;
}