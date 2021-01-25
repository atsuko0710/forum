<?php

namespace App;

use Illuminate\Support\Facades\Redis;

class Visits
{
    protected $thread;

    public function __construct($thread)
    {
        $this->thread = $thread;
    }

    /**
     * 增加计数器
     *
     * @return void
     */
    public function record()
    {
        Redis::incr($this->cacheKey());
        return $this;
    }

    /**
     * 重置计数器
     *
     * @return void
     */
    public function reset()
    {
        Redis::del($this->cacheKey());
        return $this;
    }

    /**
     * 统计计数器
     *
     * @return void
     */
    public function count()
    {
        return Redis::get($this->cacheKey()) ?: 0;
    }

    /**
     * 返回缓存键名
     *
     * @return string
     */
    public function cacheKey()
    {
        return 'threads' . $this->thread->id . 'visits';
    }
}