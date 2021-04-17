<?php

use Flarum\Extend;
use Flarum\Discussion\Search\DiscussionSearcher;
use Cosname\Search;

return [
    // Change full-text searcher
    //
    // See https://github.com/flarum/core/blob/master/tests/integration/extenders/SimpleFlarumSearchTest.php
    (new Extend\SimpleFlarumSearch(DiscussionSearcher::class))
        ->setFullTextGambit(Search\FulltextGambit::class)
];
