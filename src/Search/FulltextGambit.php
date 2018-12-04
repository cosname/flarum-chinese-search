<?php

/*
 * Modified from Flarum\Discussion\Search\Gambit\FulltextGambit.
 *
 * (c) Toby Zerner <toby.zerner@gmail.com>
 * 
 * Copyright (c) 2018 Yixuan Qiu
 * 
 */

namespace Cosname\Search;

use Flarum\Discussion\Search\DiscussionSearch;
use Flarum\Post\Post;
use Flarum\Search\AbstractSearch;
use Flarum\Search\GambitInterface;
use Illuminate\Database\Query\Expression;
use LogicException;

class FulltextGambit implements GambitInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply(AbstractSearch $search, $bit)
    {
        if (! $search instanceof DiscussionSearch) {
            throw new LogicException('This gambit can only be applied on a DiscussionSearch');
        }

        // The @ character crashes fulltext searches on InnoDB tables.
        // See https://bugs.mysql.com/bug.php?id=74042
        $bit = str_replace('@', '*', $bit);

        $query = $search->getQuery();
        $grammar = $query->getGrammar();

        // Construct a subquery to fetch discussions which contain relevant
        // posts. Retrieve the collective relevance of each discussion's posts,
        // which we will use later in the order by clause, and also retrieve
        // the ID of the most relevant post.
        
        /* $subquery = Post::whereVisibleTo($search->getActor())
            ->select('posts.discussion_id')
            ->selectRaw('SUM(MATCH('.$grammar->wrap('posts.content').') AGAINST (?)) as score', [$bit])
            ->selectRaw('SUBSTRING_INDEX(GROUP_CONCAT('.$grammar->wrap('posts.id').' ORDER BY MATCH('.$grammar->wrap('posts.content').') AGAINST (?) DESC, '.$grammar->wrap('posts.number').'), \',\', 1) as most_relevant_post_id', [$bit])
            ->where('posts.type', 'comment')
            ->whereRaw('MATCH('.$grammar->wrap('posts.content').') AGAINST (? IN BOOLEAN MODE)', [$bit])
            ->groupBy('posts.discussion_id'); */
        
        // Use the slow but exact match, http://discuss.flarum.org.cn/d/321
        $subquery = Post::whereVisibleTo($search->getActor())
            ->select('posts.discussion_id')
            ->selectRaw('count(*) as score')
            ->selectRaw('SUBSTRING_INDEX(GROUP_CONCAT('.$grammar->wrap('posts.id').' ORDER BY '.$grammar->wrap('posts.number').'), \',\', 1) as most_relevant_post_id')
            ->where('posts.type', 'comment')
            ->where('posts.content', 'like', '%' . $bit . '%')
            ->groupBy('posts.discussion_id');

        // Join the subquery into the main search query and scope results to
        // discussions that have a relevant title or that contain relevant posts.
        $query
            ->addSelect('posts_ft.most_relevant_post_id')
            ->join(
                new Expression('('.$subquery->toSql().') '.$grammar->wrap('posts_ft')),
                'posts_ft.discussion_id', '=', 'discussions.id'
            )
            ->addBinding($subquery->getBindings(), 'join')
            ->where(function ($query) use ($grammar, $bit) {
                $query->whereRaw('MATCH('.$grammar->wrap('discussions.title').') AGAINST (? IN BOOLEAN MODE)', [$bit])
                      ->orWhereNotNull('posts_ft.score');
            });

        $search->setDefaultSort(function ($query) use ($grammar, $bit) {
            $query->orderByRaw('MATCH('.$grammar->wrap('discussions.title').') AGAINST (?) desc', [$bit]);
            $query->orderBy('posts_ft.score', 'desc');
        });
    }
}
