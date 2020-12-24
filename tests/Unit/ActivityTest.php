<?php

namespace Tests\Unit;

use App\Activity;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * 活动测试
 */
class ActivityTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * 新增一个话题会多一个活动记录
     *
     * @test
     */
    public function it_records_activity_when_a_thread_is_created()
    {
        $this->signIn();
        $thread = create('App\Thread');

        $this->assertDatabaseHas('activities', [
            'type' => 'created_thread',
            'user_id' => auth()->id(),
            'subject_id' => $thread->id,
            'subject_type' => 'App\Thread'
        ]);

        $activity = Activity::first();
        $this->assertEquals($activity->subject->id, $thread->id);
    }

    /**
     * 创建一个回复有两条活动记录，一条话题、一条回复
     *
     * @test
     */
    public function it_records_activity_when_a_reply_is_created()
    {
        $this->signIn();
        create('App\Reply');

        $this->assertEquals(2, Activity::count());
    }
}