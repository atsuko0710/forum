<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Contracts\Debug\ExceptionHandler;
use App\Exceptions\Handler;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp()
    {
        parent::setUp();
        // 平时禁用异常处理
        $this->disableExceptionHanding();
    }

    /**
     * 登陆方法
     *
     * @param User $user
     * @return void
     */
    protected function signIn($user = null)
    {
        $user = $user ?: create('App\User');

        $this->actingAs($user);

        return $this;
    }

    /**
     * 禁用异常处理,直接在这里抛出
     *
     * @return void
     */
    protected function disableExceptionHanding()
    {
        $this->oldException = $this->app->make(ExceptionHandler::class);
        $this->app->instance(ExceptionHandler::class, new class extends Handler{
            public function __construct(){}
            public function report(\Exception $e){}
            public function render($request, \Exception $e)
            {
                throw $e;
            }
        });
    }

    /**
     * 不处理异常
     *
     * @return mixed
     */
    protected function withExceptionHanding()
    {
        $this->app->instance(ExceptionHandler::class, $this->oldException);
        return $this;
    }
}
