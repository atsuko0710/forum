<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class UserAvatarController extends Controller
{
    public function store()
    {
        $this->validate(request(), [
            'avatar' => ['required', 'file']
        ]);

        auth()->user()->update([
            'avatar_path' => asset(request()->file('avatar')->store('avatars', 'public'))
        ]);

        return back();
    }
}
