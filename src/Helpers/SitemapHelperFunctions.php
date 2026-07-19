<?php

namespace Otas\Sitemap\Helpers;

class SitemapHelperFunctions
{
    public static function getSitemapFilePath(): string
    {
        $fileName = config('sitemap.subdomain') ? config('sitemap.subdomain') . '-sitemap.xml' : 'sitemap.xml';
        $basePath = config('sitemap.base_path');

        return ($basePath ? rtrim($basePath, '/') . '/' : '') . 'sitemaps/' . $fileName;
    }
}