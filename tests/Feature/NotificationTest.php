<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationTest extends TestCase
{
    use DatabaseMigrations,RefreshDatabase;

    /**
     * 自己回复了话题不需要通知
     *
     * @test
     */
    public function a_notification_is_prepared_when_a_subscribed_thread_receives_a_new_reply_that_is_not_by_the_current_user()
    {
        $this->signIn();
        $thread = create('App\Thread');
        $thread->subscribe();

        // 订阅没有通知
        $this->assertCount(0, auth()->user()->notifications);

        $thread->addReply([
            'user_id' => auth()->id(),
            'body' => 'some thing'
        ]);
        $this->assertCount(0, auth()->user()->fresh()->notifications);

        $thread->addReply([
            'user_id' => create('App\User')->id,
            'body' => 'Some reply here'
        ]);
        $this->assertCount(1, auth()->user()->fresh()->notifications);
    }

    /**
     * 阅读通知
     *
     * @test
     */
    public function a_user_can_fetch_their_unread_notifications()
    {
        $this->signIn();
        $thread = create('App\Thread')->subscribe();
        
        $thread->addReply([
            'user_id' => create('App\User')->id,
            'body' => 'some things'
        ]);

        $user = auth()->user();
        $response = $this->getJson('/profile/'. $user->name . '/notifications')->json();

        $this->assertCount(1, $response);
    }

    /**
     * 清空通知
     *
     * @test
     */
    public function a_user_can_clear_a_notification()
    {
        $this->signIn();
        $thread = create('App\Thread')->subscribe();
        
        $thread->addReply([
            'user_id' => create('App\User')->id,
            'body' => 'some things'
        ]);

        $user = auth()->user();
        $this->assertCount(1, $user->unreadNotifications);

        $notificationId = $user->unreadNotifications->first()->id;

        $this->delete('/profiles/' . $user->name . '/notifications/' . $notificationId);

        $this->assertCount(0, $user->fresh()->unreadNotifications);
    }
}
