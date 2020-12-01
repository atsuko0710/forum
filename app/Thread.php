<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
 
class Thread extends Model
{
    protected $guarded = [];

    public function replies()
    {
        return $this->hasMany(Reply::class, 'thread_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // 获取话题详情链接
    public function path()
    {
        return '/threads/' . $this->id;
    }

    // 向话题添加回复
    public function addReply($reply)
    {
        return $this->replies()->create($reply);
    }
}
