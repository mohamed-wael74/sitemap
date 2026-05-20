<?php

namespace Otas\Sitemap\Helpers;

use Illuminate\Database\Eloquent\Collection;

class HandleDynamicSitemapHelper
{
    public static function buildLocalizedUrls(Collection $modelItems, array $translatedSegments, $routeKeyName = 'slug', $priority = 0.8): array
    {
        $urls = [];

        $defaultLocale = config('sitemap.default_locale');

        $locales = config('translatable.locales');

        foreach ($modelItems as $modelItem) {
            $translations = $modelItem->translations()->pluck($routeKeyName, 'locale')->toArray();

            // Ensure default locale exists in the map
            if (!isset($translations[$defaultLocale])) {
                $translations[$defaultLocale] = $modelItem->$routeKeyName;
            }

            // Build one entry for each available locale translation
            foreach ($translations as $locale => $slug) {
                $alternates = [];

                foreach ($locales as $altLocale) {
                    // Use existing or fallback to default
                    $altSlug = $translations[$altLocale] ?? $translations[$defaultLocale];
                    $segment = $translatedSegments[$altLocale];

                    $alternates[] = [
                        'hreflang' => $altLocale,
                        'href' => self::formatSitemapUrl(locale: $altLocale, segment: $segment, slug: $altSlug),
                    ];
                }

                // Add x-default
                $alternates[] = [
                    'hreflang' => 'x-default',
                    'href' => self::formatSitemapUrl(locale: $defaultLocale, segment: $translatedSegments[$defaultLocale], slug: $translations[$defaultLocale]),
                ];

                $segment = $translatedSegments[$locale];

                $urls[] = [
                    'loc' => self::formatSitemapUrl(locale: $locale, segment: $translatedSegments[$locale], slug: $slug),
                    'other_locs' => [],
                    'alternates' => $alternates,
                    'priority' => $priority,
                ];
            }
        }

        return $urls;
    }

    public static function buildDefaultUrls(Collection $modelItems, array $translatedSegments, $routeKeyName = 'slug', $priority = 0.8): array
    {
        $urls = [];
        $defaultLocale = config('sitemap.default_locale');
        $locales = config('translatable.locales');

        foreach ($modelItems as $modelItem) {
            $routeValue = $modelItem->{$routeKeyName}; // this is constant across locales

            foreach ($locales as $locale) {
                $segment = $translatedSegments[$locale] ?? $translatedSegments[$defaultLocale];

                $alternates = [];

                foreach ($locales as $altLocale) {
                    $altSegment = $translatedSegments[$altLocale] ?? $translatedSegments[$defaultLocale];

                    $alternates[] = [
                        'hreflang' => $altLocale,
                        'href' => self::formatSitemapUrl(locale: $altLocale, segment: $altSegment, slug: $routeValue),
                    ];
                }

                // Add x-default
                $alternates[] = [
                    'hreflang' => 'x-default',
                    'href' => self::formatSitemapUrl(locale: $defaultLocale, segment: $translatedSegments[$defaultLocale], slug: $routeValue),
                ];

                $urls[] = [
                    'loc' => self::formatSitemapUrl(locale: $locale, segment: $segment, slug: $routeValue),
                    'other_locs' => [],
                    'alternates' => $alternates,
                    'priority' => $priority,
                ];
            }
        }

        return $urls;
    }

    /**
     * Private helper to force WWW, absolute path, and URL encoding
     */
    private static function formatSitemapUrl(string $locale, string $segment, string $slug): string
    {
        $encodedSegment = rawurlencode($segment);
        $encodedSlug = rawurlencode($slug);
        $defaultLocale = config('sitemap.default_locale');

        $isDefaultLocale = $locale === $defaultLocale;
        $isArabic = $locale === 'ar';

        // Determine path order based on language direction
        $path = $isArabic
            ? "{$encodedSlug}/{$encodedSegment}"
            : "{$encodedSegment}/{$encodedSlug}";

        // Prepend locale if not default
        return $isDefaultLocale ? $path : "{$locale}/{$path}";
    }
}
