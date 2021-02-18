<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use RecordsActivity, Favoritable;

    protected $guarded = [];
    protected $with = ['owner', 'favorites'];
    protected $appends = ['favoritesCount', 'isFavorited', 'isBest'];

    protected static function boot()
    {
        parent::boot();

        static::created(function($reply) {
            $reply->thread->increment('replies_count');
        });

        static::deleted(function($reply) {
            if ($reply->id == $reply->thread->best_reply_id) {
                $reply->thread->update(['best_reply_id' => null]);
            }
            $reply->thread->decrement('replies_count');
        });
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * 使用修改器修改属性值，实现回复中@链接
     *
     * @param string $body
     * @return void
     */
    public function setBodyAttribute($body)
    {
        $this->attributes['body'] = preg_replace('/@([\w\-]+)/', '<a href="/profile/$1">$0</a>', $body);
    }

    /**
     * 是否被标记为最佳回复
     *
     * @return void
     */
    public function getIsBestAttribute(){
        return $this->isBest();
    }

    /**
     * 判断是否刚刚发布回复
     *
     * @return bool
     */
    public function wasJustPublished()
    {
        // 回复创建时间和当前时间做比较
        return $this->created_at->gt(Carbon::now()->subMinute());
    }

    /**
     * 匹配回复内容中匹配出来的用户名
     *
     * @return array
     */
    public function mentionedUser()
    {
        preg_match_all('/@([\w\-]+)/',$this->body,$matches);
        return $matches[1];
    }

    /**
     * 返回路径
     *
     * @return string
     */
    public function path()
    {
        return $this->thread->path();
    }

    /**
     * 判断最佳回复
     *
     * @return boolean
     */
    public function isBest()
    {
        return $this->thread->best_reply_id == $this->id;
    }
}
