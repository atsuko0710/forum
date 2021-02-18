<?php

namespace App\Http\Controllers;

use App\Reply;
use Illuminate\Http\Request;

/**
 * 最佳回复
 */
class BestRepliesController extends Controller
{
    public function store(Reply $reply)
    {
        $this->authorize('update', $reply->thread);
        $reply->thread->markBestReply($reply);
    }
}
