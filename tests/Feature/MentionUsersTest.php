<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MentionUsersTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * 在回复通知用户
     *
     * @test
     */
    public function mentioned_users_in_a_reply_are_notified()
    {
        $test = create('App\User', ['name' => 'test']);
        // test用户登陆
        $this->signIn($test);

        // 创建一个被通知的用户
        $jane = create('App\User', ['name' => 'jane']);

        $thread = create('App\Thread');
        // 创建通知
        $reply = make('App\Reply', [
            'body' => '@jane  this is test'
        ]);

        $this->post($thread->path() . '/reply', $reply->toArray());

        $this->assertCount(1, $jane->notifications);
    }
}
