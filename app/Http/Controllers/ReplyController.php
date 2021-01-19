<?php

namespace App\Http\Controllers;

use App\Inspections\Spam;
use App\Reply;
use App\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReplyController extends Controller
{

    public function __construct()
    {
        // 登陆验证
        $this->middleware('auth', ['except' => 'index']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($channelId, Thread $thread)
    {
        return $thread->replies()->paginate(20);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * 新建回复
     *
     * @param Thread $thread
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store($channelId, Thread $thread)
    {
        var_dump(Gate::denies('create', new Reply()));
        if (Gate::denies('create', new Reply())) {
            return response(
                '您回复的太过频繁', 422
            );
        }

        try {
            $this->validate(request(), [
                'body' => 'required|spamfree'
            ]);
    
            $thread->addReply([
                'body' => request('body'),
                'user_id' => auth()->id(),
            ]);
            return back()->with('flash', '成功添加回复！');
        } catch (\Exception $th) {
            return response(
                '不能提交回复', 422
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function show($channelId, Thread $thread)
    {
        return view('thread.show', compact('thread'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function edit(Reply $reply)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reply $reply)
    {
        $this->authorize('update', $reply);

        try {
            $this->validate(request(), [
                'body' => 'required|spamfree'
            ]);
    
            $reply->update(request(['body']));
        } catch (\Exception $th) {
            return response(
                '不能提交回复', 422
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reply $reply)
    {
        $this->authorize('update', $reply);
        $reply->delete();

        if (request()->expectsJson()) {
            return response(['status' => '回复删除']);
        }

        return back();
    }

    // protected function validateReply()
    // {
    //     $this->validate(request(), [
    //         'body' => 'required'
    //     ]);

    //     resolve(Spam::class)->detect(request('body'));
    // }
}
