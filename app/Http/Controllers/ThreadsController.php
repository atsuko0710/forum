<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Filters\ThreadsFilters;
use App\Thread;
use App\Trending;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class ThreadsController extends Controller
{
    public function __construct()
    {
        // 登陆验证
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * 展示话题列表
     *
     * @param Channel $channel
     * @param ThreadsFilters $filter
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index(Channel $channel, ThreadsFilters $filter, Trending $trending)
    {
        $threads = $this->getThreads($channel, $filter);

        // 如果希望返回的是json则直接返回
        if(request()->wantsJson()){
            return $threads;
        }

        // 倒序取出阅读前五的话题
        // $trending = array_map('json_decode', Redis::zrevrange('trending_threads', 0, 4));
        return view('threads.index', [
            'threads' => $threads,
            'trending' => $trending->get()
        ]);
    }

    protected function getThreads(Channel $channel, ThreadsFilters $filter)
    {
        $threads = Thread::latest()->filter($filter);

        if ($channel->exists) {
            $threads->where('channel_id', $channel->id);
        }

        $threads = $threads->paginate(10);
        return $threads;

        // 传入渠道值筛选
        // if ($channel->exists) {
        //     $threads = $channel->threads()->latest();
        // } else {
        //     $threads = Thread::latest();
        // }

        // 传入发布者筛选
        // if ($username = request('by')) {
        //     $user = User::where('name', $username)->firstOrFail();
        //     $threads->where('user_id', $user->id);
        // }
        // $threads = $threads->get();

        // $threads = $threads->filter($filter)->get();
        return $threads;
    }

    /**
     * 新增页面
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function create()
    {
        return view('threads.create');
    }

    /**
     * 创建一个话题
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|spamfree',
            'body' => 'required|spamfree',
            'channel_id' => 'required|exists:channels,id'
        ]);

        $thread = Thread::create([
            'user_id' => auth()->id(),
            'channel_id' => request('channel_id'),
            'title' => request('title'),
            'body' => request('body'),
        ]);
        return redirect($thread->path())
            ->with('flash', '成功发布话题！');
    }

    /**
     * 展示话题详情
     *
     * @param [type] $channel
     * @param Thread $thread
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function show($channel, Thread $thread, Trending $trending)
    {
        if (auth()->check()) {
            auth()->user()->read($thread);
        }

        // 阅读次数增加
        // Redis::zincrby('trending_threads', 1, json_encode([
        //     'title' => $thread->title,
        //     'path' => $thread->path()
        // ]));
        $trending->push($thread);
        // 统计该话题的阅读数
        $thread->visits()->record();

        return view('threads.show', [
            'thread' => $thread,
            'replies' => $thread->replies()->paginate(10)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function edit(Thread $thread)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Thread $thread)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy($channel, Thread $thread)
    {
        $this->authorize('update',$thread);

        $thread->delete();
        if (request()->wantsJson()) {
            return response([], 204);    
        }
        return redirect('/threads');
    }
}
