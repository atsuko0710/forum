<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ParticipateInForumTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * 经过身份验证的用户可以参与论坛话题
     * 
     * @test
     */
    public function an_authenticated_user_may_participate_in_forum_threads()
    {
        // 创建一个有权限的用户
        // $this->be(factory('App\User')->create());  // 已经登陆用户
        // $user = factory('App\User')->create();  // 未登录用户
        $this->signIn();

        // 创建一个话题
        // $thread = factory('App\Thread')->create();
        $thread = create('App\Thread');
        // 将一个回复添加到话题下
        // $reply = factory('App\Reply')->make();
        $reply = make('App\Reply');
        $this->post($thread->path().'/reply', $reply->toArray());

        $this->get($thread->path())->assertSee($reply->body);
    }

    /**
     * 未登录的用户不能添加回复
     *
     * @test
     */
    public function unauthenticated_users_may_not_add_replies()
    {
        $this->withExceptionHanding();
        // $this->expectException('Illuminate\Auth\AuthenticationException');
        // $thread = factory('App\Thread')->create();
        $thread = create('App\Thread');
        // $reply = factory('App\Reply')->create();
        $reply = create('App\Reply');
        $this->post($thread->path().'/reply', $reply->toArray())->assertRedirect('/login');
    }
}
