<?php

namespace App\Http\Controllers\Auth\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GithubAuthController extends Controller
{
    /**
     * Auth Redirect.
     * 
     */
    public function redirect()
    {
        return Socialite::driver('github')->redirect();
    }

    /**
     * Auth Callback.
     * 
     */
    public function callback()
    {
        $githubUser = Socialite::driver('github')->user();

        $user = User::updateOrCreate([
            'github_id' => $githubUser->id,
        ], [
            'name' => $githubUser->name ?? $githubUser->nickname,
            'email' => $githubUser->email,
            'github_token' => $githubUser->token,
            'github_refresh_token' => $githubUser->refreshToken,
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }
}
