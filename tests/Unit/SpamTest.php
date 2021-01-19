<?php

namespace Tests\Unit;

use App\Spam;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * 敏感词测试
 */
class SpamTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * 是否会进行关键词检测
     *
     * @test
     */
    public function it_validates_spam()
    {
        $spam = new Spam();
        $this->assertFalse($spam->detect('test'));
    }
}