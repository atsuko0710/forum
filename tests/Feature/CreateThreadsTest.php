<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreateThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * 授权用户创建新话题
     *
     * @test
     */
    public function an_authenticated_user_can_create_new_forum_thread()
    {
        // 用户授权
        // $this->be(factory('App\User')->create());
        // $this->actingAs(factory('App\User')->create());
        $this->signIn();

        // $thread = factory('App\Thread')->make();  // create 是直接入库，make是创建一个实例不入库
        $thread = make('App\Thread');  // create 是直接入库，make是创建一个实例不入库
        $this->post('/thread', $thread->toArray());  // 创建一个主题
        $this->get('/threads')->assertSee($thread->title)->assertSee($thread->body);
    }

    /**
     * 未授权用户不能创建新话题
     *
     * @test
     */
    public function guest_may_not_create_threads()
    {
        $this->expectException('Illuminate\Auth\AuthenticationException');
        // $thread = factory('App\Thread')->make();
        $thread = make('App\Thread');
        $this->post('/thread', $thread->toArray());
    }
}
