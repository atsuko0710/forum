<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * 阅读话题测试
 */
class ReadThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var Thread $thread
     */
    protected $thread;

    public function setUp(): void
    {
        parent::setUp();
        $this->thread = factory('App\Thread')->create();        
    }

    /**
     * 能够阅读话题下的回复测试
     * 
     * @test
     */
    public function a_user_can_read_replies_that_are_associated_with_a_thread()
    {
        // 如果存在 Thread
        // 并且该 Thread 拥有回复
        // $reply = factory('App\Reply')->create([
        //     'thread_id' => $this->thread->id
        // ]);

        $reply = create('App\Reply', ['thread_id' => $this->thread->id]);
        
        // 那么当我们看该 Thread 时
        // 我们也要看到回复
        $response = $this->get($this->thread->path());
        $response->assertSee($reply->body);
    }

    /**
     * 能够打开话题列表测试
     * 
     * @test
     */
    public function a_user_can_browse_threads()
    {
        $response = $this->get('/threads');
        $response->assertSee($this->thread->title);
    }

    /**
     * 能够查看单独话题测试
     * 
     * @test
     */
    public function a_user_can_read_a_single_thread()
    {
        $response = $this->get($this->thread->path());
        $response->assertSee($this->thread->title);
    }

    /**
     * 根据渠道筛选话题
     *
     * @test
     */
    public function a_user_can_filter_threads_according_to_a_channel()
    {
        $channel = create('App\Channel');
        $threadInChannel = create('App\Thread', ['channel_id' => $channel->id]);
        $threadNotInChannel = create('App\Thread');

        $this->get('/threads/' . $channel->slug)
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);
    }

    /**
     * 能够过滤自己创建的话题
     *
     * @test
     */
    public function a_user_can_filter_threads_by_any_username()
    {
        $this->signIn(create('App\User', ['name' => 'test']));        

        $threadByTest = create('App\Thread', ['user_id' => auth()->id()]);
        $threadNotByTest = create('App\Thread');

        $this->get('threads?by=test')
            ->assertSee($threadByTest->title)
            ->assertDontSee($threadNotByTest->title);
    }
}
