<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReadThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function a_user_can_read_replies_that_are_associated_with_a_thread()
    {
    // 如果存在 Thread
    // 并且该 Thread 拥有回复
    // 那么当我们看该 Thread 时
    // 我们也要看到回复
    }
}
