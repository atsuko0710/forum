<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

/**
 * 注册账号验证逻辑
 */
class RegisterConfirmationController extends Controller
{
    public function index()
    {
        // try {
        //     User::where('confirmation_token', request('token'))
        //         ->firstOrFail()
        //         ->confirm();    
        // } catch (\Throwable $th) {
        //     return redirect(route('threads'))
        //     ->with('flash', '未知的token');    
        // }
        
        // return redirect(route('threads'))
        //     ->with('flash', '你的账号已经确认');

        $user = User::where('confirmation_token', request('token'))->first();

        if (! $user) {
            return redirect(route('threads'))
                ->with('flash', '未知的token');
        }

        $user->confirm();

        return redirect(route('threads'))
                ->with('flash', '你的账号已经确认');
    }
}
