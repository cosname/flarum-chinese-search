<?php

use Flarum\Extend;
use Flarum\Discussion\Search\DiscussionSearcher;
use Cosname\Search;
use Cosname\Provider;

return [
    // Change full-text searcher
    //
    // See https://github.com/flarum/core/blob/master/tests/integration/extenders/SimpleFlarumSearchTest.php
    // (new Extend\SimpleFlarumSearch(DiscussionSearcher::class))
    //     ->setFullTextGambit(Search\FulltextGambit::class)
    //
    // Right now the method above does not work, see https://discuss.flarum.org/d/26503
    // Below is a workaround
    (new Extend\ServiceProvider)
        ->register(Provider\SearchServiceProvider::class)
];
