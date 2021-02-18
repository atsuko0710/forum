<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class BestReplyTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * 话题创建者能够指定最佳回复
     *
     * @test
     */
    public function a_thread_creator_may_mark_any_reply_as_the_best_reply()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $replies = create('App\Reply', ['thread_id' => $thread->id], 2);
        $this->assertFalse($replies[1]->isBest());

        // 指定最佳回复
        $this->postJson(route('best-replies.store', [$replies[1]->id]));
        $this->assertFalse($replies[1]->isBest());
    }

    /**
     * 确保只有话题创建者才能标记最佳话题
     *
     * @test
     */
    public function only_the_thread_creator_may_mark_a_reply_as_best()
    {
        $this->withExceptionHanding();

        // 登陆并创建话题、回复
        $this->signIn();
        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $replies = create('App\Reply', ['thread_id' => $thread->id], 2);

        // 登陆其他用户
        $this->signIn(create('App\User'));
        // 不能指定最佳回复
        $this->postJson(route('best-replies.store', [$replies[1]->id]))
            ->assertStatus(403);
        $this->assertFalse($replies[1]->fresh()->isBest());
    }
}
