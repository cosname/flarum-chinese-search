<?php

namespace Cosname\Search;

use Flarum\Core\Search\Discussion\Fulltext\DriverInterface;
use Flarum\Core\Post;

class FulltextSearch implements DriverInterface
{
    /**
     * Return an array of arrays of post IDs, grouped by discussion ID, which
     * match the given string.
     *
     * @param string $string
     * @return array
     */
    public function match($string)
    {
        // http://discuss.flarum.org.cn/d/321
        $discussionIds = Post::where('type', 'comment')
            ->where('content', 'like', '%' . $string . '%')
            ->lists('discussion_id', 'id');

        $relevantPostIds = [];

        foreach ($discussionIds as $postId => $discussionId) {
            $relevantPostIds[$discussionId][] = $postId;
        }

        return $relevantPostIds;
    }
}
