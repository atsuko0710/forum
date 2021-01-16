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
        // $this->thread = factory('App\Thread')->create();   
        $this->thread = create('App\Thread');     
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
     * 获取当前话题数据
     *
     * @test
     */
    public function a_user_can_request_all_replies_for_a_given_thread()
    {
        $reply = create('App\Reply', ['thread_id' => $this->thread->id], 2);
        $response = $this->getJson()
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

    /**
     * 能够根据评论数量排序
     *
     * @test
     */
    public function a_user_can_filter_threads_by_popularity()
    {
        // 分别取有3条、2条、0条评论的主题
        $threadWithTwoReplies = create('App\Thread');
        create('App\Reply', ['thread_id' => $threadWithTwoReplies->id], 2);
        $threadWithThreeReplies = create('App\Thread');
        create('App\Reply', ['thread_id' => $threadWithThreeReplies->id], 3);
        $thread = $this->thread;

        $response = $this->getJson('threads?popularity=1')->json();
        $this->assertEquals([3, 2, 0], array_column($response, 'replies_count'));
    }

    /**
     * 删选零回复的主题
     *
     * @test
     */
    public function a_user_can_filter_threads_by_those_that_are_unanswered()
    {
        $thread = create('App\Thread');
        $reply = create('App\Reply', ['thread_id' => $thread->id]);

        $response = $this->getJson('threads?unanswered=1')->json();
        $this->assertCount(1, $response);
    }
}
