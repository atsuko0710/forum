<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class TrendingThreadTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        Redis::del('trending_threads');
    }

    /**
     * 话题被阅读则缓存增加
     *
     * @test
     */
    public function it_increments_a_thread_score_each_time_it_is_read()
    {
        // 返回有序集合中所有数据，判断该集合为空
        $this->assertEmpty(Redis::zrevrange('trending_threads', 0, -1));

        $thread = create('App\Thread');
        $this->call('GET', $thread->path());

        $trending = Redis::zrevrange('trending_threads', 0, -1);
        $this->assertCount(1, $trending);
        $this->assertEquals($thread->title, json_decode($trending[0])->title);
    }
}
