<?php

use Flarum\Foundation\Application;
use Illuminate\Contracts\Events\Dispatcher;

return function (Dispatcher $events, Application $app) {
    $app->register(Cosname\Providers\FulltextSearchProvider::class);
};
