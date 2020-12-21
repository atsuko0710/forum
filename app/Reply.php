<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    protected $guarded = [];
    protected $with = ['owner', 'favorites'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favorited');
    }

    public function getFavoritesCountAttribute()
    {
        return $this->favorites->count();
    }

    /**
     * 保存点赞信息
     *
     * @return void
     */
    public function favorite()
    {
        $attributes = ['user_id' => auth()->id()];
        if (! $this->favorites()->where($attributes)->exists()) {
            return $this->favorites()->create($attributes);    
        }
    }

    /**
     * 判断是否之前点赞过
     *
     * @return boolean
     */
    public function isFavorited()
    {
        // return $this->favorites()->where(['user_id' => auth()->id()])->exists();
        return !! $this->favorites->where('user_id', auth()->id())->count();
    }
}
