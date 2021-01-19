<?php

namespace Tests\Feature;

use App\Activity;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

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
        $response = $this->post('/threads', $thread->toArray());  // 创建一个主题
        // $this->get('/threads')->assertSee($thread->title)->assertSee($thread->body);
        $this->get($response->headers->get('Location'))->assertSee($thread->title)->assertSee($thread->body);
    }

    /**
     * 未授权用户不能创建新话题
     *
     * @test
     */
    public function guest_may_not_create_threads()
    {
        $this->withExceptionHanding();
        // 打开新建话题页面会跳转到登陆页
        $this->get('threads/create')->assertRedirect('/login');

        // 创建话题会跳转到登陆页
        $thread = make('App\Thread');
        $this->post('/threads', $thread->toArray())->assertRedirect('/login');
    }

    /**
     * 创建话题必须包含标题
     *
     * @test
     */
    public function a_thread_requires_a_title()
    {
        $this->publishThread(['title' => null])->assertStatus(422);
    }

    /**
     * 创建话题必须包含内容
     *
     * @test
     */
    public function a_thread_requires_a_body()
    {
        $this->publishThread(['body' => null])->assertStatus(422);
    }

    /**
     * 创建话题必须包含正确规范的渠道ID
     *
     * @test
     */
    public function a_thread_requires_a_valid_channel_id()
    {
        factory('App\Channel', 2)->create();
        // 传入空渠道ID报错
        $this->publishThread(['channel_id' => null])->assertStatus(422);
        // ->assertSessionHasErrors('channel_id');

        // 传入不存在的渠道ID报错
        $this->publishThread(['channel_id' => 3])->assertStatus(422);
    }

    /**
     * 话题校验字段
     *
     * @param string $overwrite
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function publishThread($overwrite = '')
    {
        $this->withExceptionHanding()->signIn();
        $thread = make('App\Thread', $overwrite);
        $response = $this->post('/threads', $thread->toArray());
        return $response;
    }

    /**
     * 话题能被删除
     *
     * @test
     */
    public function authorized_users_can_delete_threads()
    {
        $this->signIn();
        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $reply = create('App\Reply', ['thread_id' => $thread->id]);
        $response = $this->json('DELETE', $thread->path());
        // $response = $this->delete($thread->path());

        // 正常状态，无返回数据
        $response->assertStatus(204);
        $this->assertDatabaseMissing('threads', ['id' => $thread->id]);
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);

        // $this->assertDatabaseMissing('activities', [
        //     'subject_id' => $thread->id,
        //     'subject_type' => get_class($thread),
        // ]);

        // $this->assertDatabaseMissing('activities', [
        //     'subject_id' => $reply->id,
        //     'subject_type' => get_class($reply),
        // ]);

        $this->assertEquals(0, Activity::count());
    }

    /**
     * 登陆用户才能删除话题
     *
     * @test
     */
    public function unauthorized_users_may_not_delete_threads()
    {
        $this->withExceptionHanding();
        $thread = create('App\Thread');
        $this->delete($thread->path())
            ->assertRedirect('/login');
        
        $this->signIn();
        $this->delete($thread->path())
            ->assertStatus(403);
    }
}
