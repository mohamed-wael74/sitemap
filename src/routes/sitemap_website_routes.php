<?php

use Illuminate\Support\Facades\Route;
use Otas\Sitemap\Http\Controllers\Website\SitemapController;

Route::group([
    'middleware' => [
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],
], function () {
    Route::get('/sitemap', SitemapController::class);
});
