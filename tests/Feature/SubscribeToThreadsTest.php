<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * 话题订阅功能测试
 */
class SubscribeToThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function a_user_can_subscribe_to_threads()
    {
        $this->signIn();
        $thread = create('App\Thread');

        // 订阅话题操作
        $this->post($thread->path() . '/subscriptions');
        $this->assertCount(1, $thread->subscriptions);

        // 在话题下有回复，订阅者接收到通知
        $thread->addReply([
            'user_id' => create('App\User')->id,
            'body' => '订阅测试'
        ]);
        $this->assertCount(1, auth()->user()->notifications);
    }

    /**
     * 取消订阅
     *
     * @test
     */
    public function a_user_can_unsubscribe_from_threads()
    {
        $this->signIn();
        $thread = create('App\Thread');
        $thread->subscribe();
        $this->delete($thread->path() . '/subscriptions');
        $this->assertCount(0, $thread->subscriptions);
    }
}