<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * 话题测试
 */
class ThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var Thread $thread
     */
    protected $thread;

    public function setUp(): void
    {
        parent::setUp();
        // $this->thread = factory('App\Thread')->create();
        $this->thread = create('App\Thread');
    }

    /**
     * 话题下的回复返回测试
     * 
     * @test
     */
    public function a_thread_has_replies()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->thread->replies);
    }

    /**
     * 某个话题属于创建者
     *
     * @test
     */
    public function a_thread_has_creator()
    {
        $this->assertInstanceOf('App\User', $this->thread->creator);
    }

    /**
     * 话题能够添加回复
     *
     * @test
     */
    public function a_thread_can_add_a_reply()
    {
        $this->thread->addReply([
            'body' => 'test',
            'user_id' => 1
        ]);
        $this->assertCount(1, $this->thread->replies);
    }

    /**
     * 话题能够添加回复
     *
     * @test
     */
    public function a_thread_belongs_to_a_channel()
    {
        $this->assertInstanceOf('App\Channel', $this->thread->channel);
    }

    /**
     * 访问话题详情需要在渠道下
     *
     * @test
     */
    public function a_thread_can_make_a_string_path()
    {
        $thread = create('App\Thread');
        $this->assertEquals('/threads/'.$thread->channel->slug.'/'.$thread->id, $thread->path());
    }

    /**
     * 话题订阅
     *
     * @test
     */
    public function a_thread_can_be_subscribed_to()
    {
        $thread = create('App\Thread');
        // 话题订阅
        $thread->subscribe($userId = 1);

        $this->assertEquals(
            1,
            $thread->subscriptions()->where('user_id', $userId)->count() 
        );
    }

    /**
     * 取消订阅
     *
     * @test
     */
    public function a_thread_can_be_unsubscribed_from()
    {
        $thread = create('App\Thread');
        // 话题订阅
        $thread->subscribe($userId = 1);

        $thread->unsubscribe($userId = 1);

        $this->assertEquals(
            0,
            $thread->subscriptions()->where('user_id', $userId)->count() 
        );
    }

    /**
     * 判断是否订阅
     *
     * @test
     */
    public function it_knows_if_the_authenticated_user_is_subscribed_to_it()
    {
        $thread = create('App\Thread');
        $this->signIn();
        $this->assertFalse($thread->isSubscribedTo);

        $thread->subscribe();
        $this->assertTrue($thread->isSubscribedTo);
    }
}
