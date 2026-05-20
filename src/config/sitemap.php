<?php

return [
    /*
    |--------------------------------------------------------------------------
    | sitemap Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the options for the Otas sitemap package.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Package routes prefix
    |--------------------------------------------------------------------------
    |
    | Here you may prefer to union the usage of your project routes with a global
    | prefix. Define this preferred prefix and access package predefined
    | routes under the same project global prefix to union the output
    | of your apis.
    |
    */
    'package_routes_prefix' => 'api',

    /*
    |--------------------------------------------------------------------------
    | Routes publish subdirectory
    |--------------------------------------------------------------------------
    |
    | Here you may specify the subdirectory where the package routes should be
    | published inside the project's routes folder. This allows you to customize the location of the published
    | routes files.
    |
    */
    # eg: 'routes_publish_subdirectory' => 'custom/',
    'routes_publish_subdirectory' => '',

    /*
    |--------------------------------------------------------------------------
    | Website URL
    |--------------------------------------------------------------------------
    |
    | The base URL of your website. This is used as the root for all sitemap
    | links. Example: 'https://example.com'
    */
    'website_url' => null,

    /*
    |--------------------------------------------------------------------------
    | Subdomain
    |--------------------------------------------------------------------------
    |
    | The subdomain to be used for sitemap generation, if applicable. Leave as
    | null if not using subdomains.
    */
    'subdomain' => null,

    /*
    |--------------------------------------------------------------------------
    | Default Locale
    |--------------------------------------------------------------------------
    |
    | The default language/locale for the sitemap. Used for alternate language
    | links and localization. Example: 'en'
    */
    'default_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Static Links
    |--------------------------------------------------------------------------
    |
    | An array of static links to always include in the sitemap. Each link can
    | define its location, alternate locales, and priority. Example structure
    | is provided in the commented section below.
    */
    'static_links' => [
        // [
        //     'loc' => 'ar',
        //     'other_locs' => ['en'],
        //     'alternates' => [
        //         ['hreflang' => 'ar', 'href' => 'ar'],
        //         ['hreflang' => 'en', 'href' => 'en'],
        //         ['hreflang' => 'x-default', 'href' => ''],
        //     ],
        //     'priority' => '1.0',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Dynamic Links
    |--------------------------------------------------------------------------
    |
    | An array of model classes whose records will be used to generate dynamic
    | sitemap links. Each class should implement the necessary interface or
    | provide required methods for link generation.
    */
    'dynamic_links' => [
        // App\Models\Example::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Load Routes
    |--------------------------------------------------------------------------
    |
    | This option controls whether the package routes should be loaded.
    | Set this value to true to load the routes, or false to disable them.
    |
    */
    'load_routes' => true,
];
