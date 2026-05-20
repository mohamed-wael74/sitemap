<?php

namespace Otas\Sitemap\Http\Controllers\Website;

use Illuminate\Support\Facades\Storage;
use Otas\Sitemap\Http\Controllers\Controller;
use Otas\Sitemap\Helpers\SitemapHelperFunctions;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $filePath = SitemapHelperFunctions::getSitemapFilePath();

        $xml = Storage::disk('public')->get($filePath);

        return response($xml);
    }
}
