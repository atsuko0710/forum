<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $guarded = [];

    // 修改隐式绑定路由
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
