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
    public function a_reply_has_an_owner()
    {
        // $reply = factory('App\Reply')->create(); 
        $reply = create('App\Reply'); 
        $this->assertInstanceOf('App\User', $reply->owner);
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

    /**
     * 回复内容中匹配获取用户名
     *
     * @test
     */
    public function it_can_detect_all_mentioned_users_in_the_body()
    {
        $reply = create('App\Reply', [
            'body' => '@Jane talk to @Jane'
        ]);
        $this->assertEquals(['Jane', 'Jane'], $reply->mentionedUser());
    }

    /**
     * 被@的用户增加个人界面链接
     *
     * @test
     */
    public function it_warps_mentioned_usernames_in_the_body_within_archor_tags()
    {
        $reply = create('App\Reply', [
            'body' => 'talk to @Jane'
        ]);
        $this->assertEquals(
            'talk to <a href="/profiles/Jane">@Jane</a>',
            $reply->body
        );
    }
}
