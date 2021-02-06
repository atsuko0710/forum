<?php

namespace Tests\Feature;

use App\Trending;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class TrendingThreadTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        // Redis::del('trending_threads');

        $this->trending = new Trending();
        $this->trending->reset();
    }

    /**
     * 话题被阅读则缓存增加
     *
     * @test
     */
    public function it_increments_a_thread_score_each_time_it_is_read()
    {
        // 返回有序集合中所有数据，判断该集合为空
        $this->assertEmpty($this->trending->get());

        $thread = create('App\Thread');
        
        $this->call('GET', $thread->path());

        // $trending = Redis::zrevrange('testing_trending_threads', 0, -1);
        $this->assertCount(1, $trending = $this->trending->get());
        $this->assertEquals($thread->title, $trending[0]->title);
    }
}
