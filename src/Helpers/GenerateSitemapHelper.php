<?php

namespace Otas\Sitemap\Helpers;

use DOMDocument;
use DOMElement;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateSitemapHelper
{
    public static function generate()
    {
        $staticLinks = HandleStaticSitemapHelper::buildUrls();

        $dynamicUrls = [];

        foreach (config('sitemap.dynamic_links') as $modelClass):
            if (\method_exists($modelClass, 'buildSitemapUrls')) {
                $modelUrls = $modelClass::buildSitemapUrls();

                $dynamicUrls = array_merge($dynamicUrls, $modelUrls);
            }
        endforeach;

        $urls = [...$staticLinks, ...$dynamicUrls];

        $xml = self::buildXml($urls);

        $filePath = SitemapHelperFunctions::getSitemapFilePath();

        return Storage::disk('public')->put($filePath, $xml);
    }

    private static function buildXml(array $urls): string
    {
        $websiteUrl = self::prepareWebsiteUrl();

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $NS = 'http://www.sitemaps.org/schemas/sitemap/0.9';
        $XHTML_NS = 'http://www.w3.org/1999/xhtml';

        $urlset = $dom->createElementNS($NS, 'urlset');
        $urlset->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xhtml', $XHTML_NS);
        $dom->appendChild($urlset);

        foreach ($urls as $urlData) {

            $url = self::createUrl(
                dom: $dom,
                websiteUrl: $websiteUrl,
                loc: $urlData['loc'],
                alternates: $urlData['alternates'],
                priority: $urlData['priority']
            );

            $urlset->appendChild($url);

            foreach ($urlData['other_locs'] as $otherLoc):
                $url = self::createUrl(
                    dom: $dom,
                    websiteUrl: $websiteUrl,
                    loc: $otherLoc,
                    alternates: $urlData['alternates'],
                    priority: $urlData['priority']
                );

                $urlset->appendChild($url);
            endforeach;
        }

        return $dom->saveXML();
    }

    private static function createUrl(DOMDocument $dom, string $websiteUrl, string $loc, array $alternates, string $priority): DOMElement
    {
        $url = $dom->createElement('url');

        $locUrl = $loc ? $websiteUrl . '/' . $loc : $websiteUrl;
        $loc = $dom->createElement('loc', $locUrl);
        $url->appendChild($loc);

        foreach ($alternates as $alt) {
            $href = $alt['href'] ? $websiteUrl . '/' . $alt['href'] : $websiteUrl;

            $link = $dom->createElement('xhtml:link');
            $link->setAttribute('rel', 'alternate');
            $link->setAttribute('hreflang', $alt['hreflang']);
            $link->setAttribute('href', $href);
            $url->appendChild($link);
        }

        $url->appendChild($dom->createElement('lastmod', now()->toAtomString()));
        $url->appendChild($dom->createElement('changefreq', 'daily'));
        $url->appendChild($dom->createElement('priority', $priority));

        return $url;
    }

    private static function prepareWebsiteUrl(): string
    {
        $websiteUrl = config('sitemap.website_url') ?? \str_replace('api.', '', config('app.url'));

        $hasSubdomain = (bool) config('sitemap.subdomain');

        if ($hasSubdomain) {
            $baseUrl = parse_url($websiteUrl);
            $websiteUrl = $baseUrl['scheme'] . '://' . config('sitemap.subdomain') . '.' . $baseUrl['host'];
        }

        if (!$hasSubdomain && !Str::contains($websiteUrl, '://www.')) {
            $websiteUrl = str_replace('://', '://www.', $websiteUrl);
        }

        return $websiteUrl;
    }
}
