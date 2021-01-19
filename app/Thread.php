<?php

namespace App;

use App\Events\ThreadHasNewReply;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use RecordsActivity;

    protected $guarded = [];
    protected $with = ['creator','channel'];
    protected $appends = ['isSubscribedTo'];

    // 定义全局作用域
    protected static function boot()
    {
        parent::boot();

        // static::addGlobalScope('replyCount',function ($builder){
        //    $builder->withCount('replies');
        // });

        // 删除一条话题，其下回复也要删除
        static::deleting(function($thread) {
            $thread->replies->each->delete();
        });
    }
    
    public function replies()
    {
        // return $this->hasMany(Reply::class, 'thread_id', 'id');
        // return $this->hasMany(Reply::class, 'thread_id', 'id')
        //             ->withCount('favorites')
        //             ->with('owner');
        return $this->hasMany(Reply::class, 'thread_id', 'id');  // 预加载已经在Reply进行了
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

    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
            ->where('user_id', auth()->id())
            ->exists();
    }

    /**
     * 订阅
     *
     * @param int $userId
     * @return void
     */
    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?: auth()->id()
        ]);
        return $this;
    }

    /**
     * 取消订阅
     *
     * @param int $userId
     * @return void
     */
    public function unsubscribe($userId = null)
    {
        $this->subscriptions()
            ->where('user_id', $userId ?: auth()->id())
            ->delete();
        return $this;
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
        $reply = $this->replies()->create($reply);
        // 改为用事件来触发通知
        event(new ThreadHasNewReply($this, $reply));

        return $reply;

        // 向订阅用户添加通知
        $this->subscriptions
            // ->filter(function($sub) use ($reply) {
            //     return $sub->user_id != $reply->user_id;
            // })
            ->where('user_id', '!=', $reply->user_id)
            ->each->notify($reply);

        return $reply;
    }

    /**
     * 判断话题是否能被更新
     *
     * @return boolean
     */
    public function hasUpdatesFor($user)
    {
        $key = $user->visitedThreadCacheKey($this);
        // 返回话题更新时间是否大于用户阅读该话题的时间，如果是则说明此时该用户没有阅读过该话题
        return $this->updated_at > cache($key);
    }
}
