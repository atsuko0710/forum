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
}