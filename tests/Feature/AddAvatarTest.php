<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AddAvatarTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * 未登录不能上传头像
     *
     * @test
     */
    public function only_members_can_add_avatars()
    {
        $this->withExceptionHanding();

        $this->json('POST', 'api/users/1/avatar')
            ->assertStatus(401);
    }

    /**
     * 上传的头像需要是有效内容
     *
     * @test
     */
    public function a_valid_avatar_must_be_provided()
    {
        $this->withExceptionHanding();
        $this->signIn();

        $this->json('POST', 'api/users/' . auth()->id() . '/avatar', [
            'avatar' => 'not-an-avatar'
        ])->assertStatus(422);
    }

    /**
     * 测试正常上传
     *
     * @test
     */
    public function a_user_may_add_an_avatar_to_their_profile()
    {
        $this->signIn();
        
        Storage::fake('public');

        $this->json('POST', 'api/users/' . auth()->id() . '/avatar', [
            'avatar' => $file = UploadedFile::fake()->image('avatar.jpg')
        ]);

        $this->assertEquals('avatars/' . $file->hashName(), auth()->user()->avatar_path);
    }
}
