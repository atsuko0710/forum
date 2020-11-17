<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var Thread $thread
     */
    protected $thread;

    public function setUp()
    {
        parent::setUp();
        $this->thread = factory('App\Thread')->create();        
    }

    /**
     * @test
     */
    public function a_user_can_browse_threads()
    {
        $response = $this->get('/threads');
        $response->assertSee($this->thread->title);
    }

    /**
     * @test
     */
    public function a_user_can_read_a_single_thread()
    {
        $response = $this->get('/thread/'.$this->thread->id);
        $response->assertSee($this->thread->title);
    }
}
