<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * 回复测试
 */
class ReplyTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * 回复属于某个用户测试
     * 
     * @test
     */
    public function a_replay_has_an_owner()
    {
        $replay = factory('App\Reply')->create();        
        $this->assertInstanceOf('App\User', $replay->owner);
    }
}
