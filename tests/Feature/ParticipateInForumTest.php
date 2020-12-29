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

        // $this->get($thread->path())->assertSee($reply->body);

        $this->assertDatabaseHas('replies', ['body' => $reply->body]);        
        $this->assertEquals(1, $thread->fresh()->replies_count);
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

    /**
     * 一个回复必须包含内容
     *
     * @test
     */
    public function a_reply_require_a_body()
    {
        $this->withExceptionHanding()->signIn();
        $thread = create('App\Thread');
        $reply = make('App\Reply', ['body' => null]);
        $this->post($thread->path().'/reply', $reply->toArray())->assertSessionHasErrors('body');
    }

    /**
     * 没有登陆的用户不能删除回复
     *
     * @test
     */
    public function unauthorized_users_cannot_delete_replies()
    {
        $this->withExceptionHanding();
        $reply = create('App\Reply');
        $this->delete('/replies/'.$reply->id)->assertRedirect('/login');

        $this->signIn();
        $this->delete('/replies/'.$reply->id)
            ->assertStatus(403);
    }
    
    /**
     * 有权限的用户能够正常删除数据
     *
     * @test
     */
    public function authorized_users_can_delete_replies()
    {
        $this->signIn();
        $reply = create('App\Reply', ['user_id' => auth()->id()]);
        $this->delete('/replies/'.$reply->id);
        // 数据删除
        $this->assertDatabaseMissing('replies', [
            'id' => $reply->id
        ]);
        // 回复数量变为0
        $this->assertEquals(0, $reply->replies_count);
    }

    /**
     * 登陆用户能够修改回复
     *
     * @test
     */
    public function authorized_users_can_update_replies()
    {
        $this->signIn();
        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $updateReply = '更新回复';
        $this->patch('/replies/'.$reply->id, ['body' => $updateReply]);

        $this->assertDatabaseHas('replies', [
            'id' => $reply->id,
            'body' => $updateReply
        ]);
    }

    /**
     * 没有授权的用户不能更新回复
     *
     * @test
     */
    public function unauthorized_users_cannot_update_replies()
    {
        $this->withExceptionHanding();
        $reply = create('App\Reply');

        $this->patch('/replies/'.$reply->id)->assertRedirect('login');

        $this->signIn();
        $this->patch('/replies/'.$reply->id)->assertStatus(403);
    }
}
