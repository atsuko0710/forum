<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * 阅读话题测试
 */
class ProfilesTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * 一个用户有个人信息
     *
     * @test
     */
    public function a_user_has_a_profile()
    {
        $user = create('App\User');
        $this->get('profile/'.$user->name)->assertSee($user->name);
    }

    /**
     * 用户信息界面显示发布话题
     *
     * @test
     */
    public function profiles_display_all_threads_created_by_the_associated_user()
    {
        // $user = create('App\User');
        $this->signIn();
        $thead = create('App\Thread', ['user_id' => auth()->id()]);
        $this->get('profile/'.auth()->user()->name)
            ->assertSee($thead->title)
            ->assertSee($thead->body);
    }
}