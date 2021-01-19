<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * 回复测试
 */
class ReplyTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * 回复属于某个用户测试
     * 
     * @test
     */
    public function a_replay_has_an_owner()
    {
        // $replay = factory('App\Reply')->create(); 
        $replay = create('App\Reply'); 
        $this->assertInstanceOf('App\User', $replay->owner);
    }

    /**
     * 从回复能够判断是刚刚发布
     *
     * @test
     */
    public function it_knows_if_it_was_just_published()
    {
        // 创建一个回复
        $reply = create('App\Reply');
        // 判断该回复是刚刚发布
        $this->assertTrue($reply->wasJustPublished());

        // 修改回复创建时间为上个月
        $reply->created_at = Carbon::now()->subMonth();
        $this->assertFalse($reply->wasJustPublished());
    }
}
