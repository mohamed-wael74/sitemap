<?php

namespace Otas\Sitemap\Helpers;

use Illuminate\Support\Str;

class HandleStaticSitemapHelper
{
    public static function buildUrls(): array
    {
        $urls = [];

        $staticLinks = config('sitemap.static_links');

        foreach ($staticLinks as $staticLink) {

            $otherLocs = [];

            foreach ($staticLink['other_locs'] as $otherLoc):
                $otherLocs[] = rawurlencode($otherLoc);
            endforeach;

            $alternates = [];
            foreach ($staticLink['alternates'] as $alternate):

                if (Str::contains($alternate['href'], "{$alternate['hreflang']}/")) {
                    $href = str_replace("{$alternate['hreflang']}/", '', $alternate['href']);
                    $href = $alternate['hreflang'] . '/' . rawurlencode($href);
                } else {
                    $href = rawurlencode($alternate['href']);
                }

                $alternates[] = [
                    'hreflang' => $alternate['hreflang'],
                    'href' => $href,
                ];
            endforeach;

            $urls[] = [
                'loc' => $staticLink['loc'],
                'other_locs' => $otherLocs,
                'alternates' => $alternates,
                'priority' => $staticLink['priority'],
            ];

        }

        return $urls;
    }
}
