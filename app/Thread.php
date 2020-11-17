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

    public function path()
    {
        return '/thread/' . $this->id;
    }
}
