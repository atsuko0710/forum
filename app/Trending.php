<?php

namespace App;

use Illuminate\Support\Facades\Redis;

class Trending
{
    /**
     * 获取阅读排名前五的数据
     *
     * @return void
     */
    public function get()
    {
        return array_map('json_decode', Redis::zrevrange($this->cacheKey(), 0, 4));
    }

    /**
     * 阅读数增加
     *
     * @param Thread $thread
     * @return void
     */
    public function push(Thread $thread)
    {
        Redis::zincrby($this->cacheKey(), 1, json_encode([
            'title' => $thread->title,
            'path' => $thread->path()
        ]));
    }

    /**
     * 重置缓存
     *
     * @return void
     */
    public function reset()
    {
        Redis::del($this->cacheKey());
    }

    /**
     * 获取排名的集合key值
     *
     * @return string
     */
    public function cacheKey()
    {
        return app()->environment('testing')
            ? 'testing_trending_threads'
            : 'trending_threads';
    }
}