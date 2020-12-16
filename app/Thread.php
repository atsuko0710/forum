<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    protected $guarded = [];

    // 定义全局作用域
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('replyCount',function ($builder){
           $builder->withCount('replies');
        });
    }
    
    public function replies()
    {
        return $this->hasMany(Reply::class, 'thread_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class, 'channel_id', 'id');
    }

    /**
     * 本地作用域，做查询
     *
     * @param \Illuminate\Database\Eloquent\Builder $query 查询构造器
     * @param \App\Filters\ThreadsFilters $filters
     * @return void
     */
    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }

    // 获取话题详情链接
    public function path()
    {
        // return '/threads/' . $this->id;
        return '/threads/'.$this->channel->slug.'/'.$this->id;
    }

    // 向话题添加回复
    public function addReply($reply)
    {
        return $this->replies()->create($reply);
    }
}
