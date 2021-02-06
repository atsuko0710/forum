<?php

namespace App;

use App\Events\ThreadHasNewReply;
use App\Events\ThreadReceivedNewReply;
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

    // 修改隐式绑定路由
    public function getRouteKeyName()
    {
        return 'slug';
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

    public function setSlugAttribute($value){
        if (static::whereSlug($slug = str_slug($value))->exists()) {
            $slug = $this->incrementSlug($slug);
        }
        $this->attributes['slug'] = $slug;
    }

    /**
     * 修改slug
     *
     * @param string $slug
     * @return string
     */
    private function incrementSlug($slug)
    {
        // 取出最大ID话题的 slug 值
        $max = static::whereTitle($this->title)->latest('id')->value('slug');

        // 判断最后一个字符是否为数字
        if (is_numeric($max[-1])) {
            // 将数字匹配出来
            return preg_replace_callback('/(\d+)$/', function ($matches) {
                // 自增1
                return $matches[1] + 1;
            }, $max);
        }
        // 如果不是数字则后缀为2
        return $slug . '-2';
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
        return '/threads/'.$this->channel->slug.'/'.$this->slug;
    }

    // 向话题添加回复
    public function addReply($reply)
    {
        $reply = $this->replies()->create($reply);
        // event(new ThreadHasNewReply($this, $reply));
        // 在回复中增加 @ 通知,触发通知,两个通知合并到同一个事件
        event(new ThreadReceivedNewReply($reply));

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

    /**
     * 声明 visits 类，记录浏览量
     *
     * @return Visits
     */
    public function visits()
    {
        return new Visits($this);
    }
}
