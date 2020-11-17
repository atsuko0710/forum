<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReplyTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function a_replay_has_an_owner()
    {
        $replay = factory('App\Reply')->create();        
        $this->assertInstanceOf('App\User', $replay->owner);
    }
}
