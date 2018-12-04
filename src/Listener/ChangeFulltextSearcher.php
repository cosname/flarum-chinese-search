<?php
/*
* Copyright (c) 2018 Yixuan Qiu
*/

namespace Cosname\Listener;

use Flarum\Event\ConfigureDiscussionGambits;
use Illuminate\Contracts\Events\Dispatcher;

class ChangeFulltextSearcher
{
    /**
     * Subscribes to the Flarum events.
     *
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(ConfigureDiscussionGambits::class, [$this, 'changeFulltextSearcher']);
    }

    /**
     * Change the full-text searcher.
     *
     * @param Serializing $event
     */
    public function changeFulltextSearcher(ConfigureDiscussionGambits $event)
    {
        $event->gambits->setFulltextGambit('Cosname\Search\FulltextGambit');
    }
}
