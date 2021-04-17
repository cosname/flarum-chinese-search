<?php

namespace Cosname\Provider;

use Flarum\Foundation\AbstractServiceProvider;
use Flarum\Discussion\Search\DiscussionSearcher;
use Cosname\Search\FulltextGambit;

class SearchServiceProvider extends AbstractServiceProvider
{
    public function register()
    {
        // Workaround for https://github.com/flarum/core/issues/2712
        $this->container->extend('flarum.simple_search.fulltext_gambits', function ($oldFulltextGambits) {
            $oldFulltextGambits[DiscussionSearcher::class] = FulltextGambit::class;

            return $oldFulltextGambits;
        });
    }
}
