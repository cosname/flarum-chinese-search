<?php

use Cosname\Listener;
use Illuminate\Contracts\Events\Dispatcher;

return [
    // Add listener
    function (Dispatcher $events) {
        $events->subscribe(Listener\ChangeFulltextSearcher::class);
    }
];
