<?php

namespace App\Policies;

use App\Reply;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReplyPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, Reply $reply)
    {
        return $reply->user_id == $user->id;
    }

    public function create(User $user)
    {
        return true;
        // 当一个用户从来没有回复过
        if (! $lastReply = $user->fresh()->lastReply) {
            return true;
        }
        return ! $lastReply->wasJustPublished();
    }
}
