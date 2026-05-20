<?php

namespace Otas\Sitemap\Helpers;

class SitemapHelperFunctions
{
    public static function getSitemapFilePath(): string
    {
        $fileName =  config('sitemap.subdomain') ? config('sitemap.subdomain') . '-sitemap.xml' : 'sitemap.xml';

        return 'sitemaps/' . $fileName;
    }
}