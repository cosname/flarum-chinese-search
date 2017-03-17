<?php

namespace Cosname\Providers;

use Illuminate\Support\ServiceProvider;

class FulltextSearchProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'Flarum\Core\Search\Discussion\Fulltext\DriverInterface',
            'Cosname\Search\FulltextSearch'
        );
    }
}
