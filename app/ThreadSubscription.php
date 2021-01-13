<?php

namespace App;

use App\Notifications\ThreadWasUpdate;
use Illuminate\Database\Eloquent\Model;

class ThreadSubscription extends Model
{
    protected $guarded =[];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    // 创建通知
    public function notify($reply)
    {
        $this->user->notify(new ThreadWasUpdate($this->thread, $reply));
    }
}
