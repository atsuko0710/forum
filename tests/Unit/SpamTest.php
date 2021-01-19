<?php

namespace Tests\Unit;

use App\Inspections\Spam;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * 敏感词测试
 */
class SpamTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * 是否会进行关键词检测（判断方法是否正常适用）
     *
     * @test
     */
    public function it_validates_spam()
    {
        $spam = new Spam();
        $this->assertFalse($spam->detect('test'));
    }

    /**
     * 检测重复出现的词
     *
     * @test
     */
    public function it_checks_for_any_being_held_down(){
        $spam = new Spam();
        $this->expectException(\Exception::class);
        $spam->detect('errddddddddddd');
    }
}