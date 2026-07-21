<?php

namespace Otas\Sitemap\Helpers;

use Illuminate\Database\Eloquent\Collection;

class HandleDynamicSitemapHelper
{
    /**
     * Build sitemap URLs for models with translated slugs (e.g. blogs).
     *
     * The :modelIdentifier placeholder is automatically resolved to the translated
     * model identifier for each locale. Any additional placeholders can be resolved via $slugResolver.
     *
     */
    public static function buildLocalizedUrls(
        Collection $modelItems,
        array $translatedSegments,
        ?callable $slugResolver = null,
        string $routeKeyName = 'slug',
        float $priority = 0.8
    ): array {
        $urls = [];
        $defaultLocale = config('sitemap.default_locale');
        $locales = config('translatable.locales');

        foreach ($modelItems as $modelItem) {
            $translations = $modelItem->translations()->pluck($routeKeyName, 'locale')->toArray();

            // Ensure default locale exists in the map
            if (!isset($translations[$defaultLocale])) {
                $translations[$defaultLocale] = $modelItem->$routeKeyName;
            }

            // Resolve extra placeholders once per model item
            $extraReplacements = $slugResolver ? $slugResolver($modelItem) : [];

            // Build one entry for each available locale translation
            foreach ($translations as $locale => $slug) {
                $alternates = [];

                foreach ($locales as $altLocale) {
                    $altSlug = $translations[$altLocale] ?? $translations[$defaultLocale];
                    $altTemplate = $translatedSegments[$altLocale] ?? $translatedSegments[$defaultLocale];

                    $alternates[] = [
                        'hreflang' => $altLocale,
                        'href' => self::formatSitemapUrl(
                            locale: $altLocale,
                            path: self::resolvePlaceholders(template: $altTemplate, modelIdentifierValue: $altSlug, extraReplacements: $extraReplacements),
                        ),
                    ];
                }

                // Add x-default
                $defaultTemplate = $translatedSegments[$defaultLocale];

                $alternates[] = [
                    'hreflang' => 'x-default',
                    'href' => self::formatSitemapUrl(
                        locale: $defaultLocale,
                        path: self::resolvePlaceholders(template: $defaultTemplate, modelIdentifierValue: $translations[$defaultLocale], extraReplacements: $extraReplacements),
                    ),
                ];

                $template = $translatedSegments[$locale] ?? $translatedSegments[$defaultLocale];

                $urls[] = [
                    'loc' => self::formatSitemapUrl(
                        locale: $locale,
                        path: self::resolvePlaceholders(template: $template, modelIdentifierValue: $slug, extraReplacements: $extraReplacements),
                    ),
                    'other_locs' => [],
                    'alternates' => $alternates,
                    'priority' => $priority,
                ];
            }
        }

        return $urls;
    }

    /**
     * Build sitemap URLs for models with non-translated slugs (e.g. offers).
     *
     * The :modelIdentifier placeholder is automatically resolved to $modelItem->{$routeKeyName}.
     * Any additional placeholders can be resolved via $slugResolver.
     *
     */
    public static function buildDefaultUrls(
        Collection $modelItems,
        array $translatedSegments,
        ?callable $slugResolver = null,
        string $routeKeyName = 'slug',
        float $priority = 0.8
    ): array {
        $urls = [];
        $defaultLocale = config('sitemap.default_locale');
        $locales = config('translatable.locales');

        foreach ($modelItems as $modelItem) {
            $routeValue = $modelItem->{$routeKeyName};

            // Resolve extra placeholders once per model item
            $extraReplacements = $slugResolver ? $slugResolver($modelItem) : [];

            foreach ($locales as $locale) {
                $template = $translatedSegments[$locale] ?? $translatedSegments[$defaultLocale];

                $alternates = [];

                foreach ($locales as $altLocale) {
                    $altTemplate = $translatedSegments[$altLocale] ?? $translatedSegments[$defaultLocale];

                    $alternates[] = [
                        'hreflang' => $altLocale,
                        'href' => self::formatSitemapUrl(
                            locale: $altLocale,
                            path: self::resolvePlaceholders(template: $altTemplate, modelIdentifierValue: $routeValue, extraReplacements: $extraReplacements),
                        ),
                    ];
                }

                // Add x-default
                $defaultTemplate = $translatedSegments[$defaultLocale];

                $alternates[] = [
                    'hreflang' => 'x-default',
                    'href' => self::formatSitemapUrl(
                        locale: $defaultLocale,
                        path: self::resolvePlaceholders(template: $defaultTemplate, modelIdentifierValue: $routeValue, extraReplacements: $extraReplacements),
                    ),
                ];

                $urls[] = [
                    'loc' => self::formatSitemapUrl(
                        locale: $locale,
                        path: self::resolvePlaceholders(template: $template, modelIdentifierValue: $routeValue, extraReplacements: $extraReplacements),
                    ),
                    'other_locs' => [],
                    'alternates' => $alternates,
                    'priority' => $priority,
                ];
            }
        }

        return $urls;
    }

    /**
     * Replace :modelIdentifier and any extra placeholders in the path template.
     */
    private static function resolvePlaceholders(string $template, string $modelIdentifierValue, array $extraReplacements = []): string
    {
        $replacements = array_merge([':modelIdentifier' => $modelIdentifierValue], $extraReplacements);

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Format a sitemap URL path with proper encoding and locale prefix.
     */
    private static function formatSitemapUrl(string $locale, string $path): string
    {
        $encodedPath = self::encodePath($path);

        $isArabic = $locale === 'ar';

        return $isArabic ? $encodedPath : "{$locale}/{$encodedPath}";
    }

    /**
     * Encode each segment of a path individually while preserving slashes.
     */
    private static function encodePath(string $path): string
    {
        $segments = explode('/', $path);

        $encoded = array_map(fn(string $segment) => rawurlencode($segment), $segments);

        return implode('/', $encoded);
    }
}
