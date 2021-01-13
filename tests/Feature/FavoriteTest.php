<?php

namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class FavoriteTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * 登陆用户能点赞回复
     *
     * @test
     */
    public function an_authenticated_user_can_favorite_any_reply()
    {
        $this->signIn();
        $reply = create('App\Reply');
        $this->post('replies/'.$reply->id.'/favorite');
        $this->assertCount(1, $reply->favorites);
    }

    /**
     * 没有登陆的用户不能点赞
     *
     * @test
     */
    public function guests_can_not_favorite_anything()
    {
        $this->withExceptionHanding();
        $this->post('replies/1/favorite')->assertRedirect('/login');
    }

    /**
     * 同一个用户不能点赞多次
     *
     * @test
     */
    public function an_authenticated_user_may_only_favorite_a_reply_once()
    {
        $this->signIn();
        $reply = create('App\Reply');
        try {
            $this->post('replies/'.$reply->id.'/favorite');
            $this->post('replies/'.$reply->id.'/favorite');    
        } catch (\Throwable $th) {
            $this->fail('同一个用户不能点赞多次');
        }
        
        $this->assertCount(1, $reply->favorites);
    }

    /**
     * 取消点赞
     *
     * @test
     */
    public function an_authenticated_user_can_unfavorite_a_reply()
    {
        $this->signIn();
        $reply = create('App\Reply');

        $this->post('replies/'.$reply->id.'/favorite');
        $this->assertCount(1, $reply->favorites);

        $this->delete('replies/'.$reply->id.'/favorite');
        $this->assertCount(0, $reply->fresh()->favorites);
    }
} 