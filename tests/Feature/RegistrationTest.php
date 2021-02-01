<?php

namespace Tests\Feature;

use App\Mail\PleaseConfirmYourEmail;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * 邮箱验证
     *
     * @test
     */
    public function a_confirmation_email_is_sent_upon_registration()
    {
        Mail::fake();

        // event(new Registered(create('App\User')));
        $this->post(route('register'), [
            'name' => 'NoNo1',
            'email' => 'atsukodan10@163.com',
            'password' => '123456',
            'password_confirmation' => '123456',
        ]);

        Mail::assertSent(PleaseConfirmYourEmail::class);
    }

    /**
     * 用户点击邮箱链接，成功认证
     *
     * @test
     */
    public function user_can_fully_confirm_their_email_addresses()
    {
        $this->post(route('register'), [
            'name' => 'NoNo1',
            'email' => 'atsukodan10@163.com',
            'password' => '123456',
            'password_confirmation' => '123456',
        ]);

        $user = User::whereName('NoNo1')->first();

        // 新用户未认证且有 confirmation_token
        $this->assertFalse($user->confirmed);
        $this->assertNotNull($user->confirmation_token);

        $this->get(route('register.confirm', [
            'token' => $user->confirmation_token
        ]))
        ->assertRedirect(route('threads'));

        // $this->assertTrue($user->fresh()->confirmed);
        // $response->assertRedirect('/threads');

        tap($user->fresh(), function($user) {
            $this->assertTrue($user->confirmed);
            $this->assertNull($user->confirmation_token);
        });
    }

    /**
     * 测试无效的校验
     *
     * @test
     */
    public function confirming_an_invalid_token()
    {
        $this->get(route('register.confirm', ['token' => 'invalid']))
            ->assertRedirect(route('threads'))
            ->assertSessionHas('flash', '未知的token');
    }
}
