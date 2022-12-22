<?php

namespace App\Extensions;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Support\Facades\Session;
use Kreait\Laravel\Firebase\Facades\Firebase;
use stdClass;

class FirebaseAuthGuard implements Guard
{
    use GuardHelpers;

    public function __construct()
    {
        $this->auth = Firebase::auth();
    }

    public function user()
    {
        $token = Session::get('token');

        if (!$token) {
            return;
        }

        try {
            $data = $this->auth->verifySessionCookie($token)->claims()->all();
        } catch (\Throwable) {
            return;
        }

        return json_decode(json_encode($data));
    }

    public function validate(array $credentials = [])
    {
        //
    }

    public function attempt(array $credentials)
    {
        try {
            $firebaseUser = $this->auth->signInWithEmailAndPassword(
                $credentials['email'],
                $credentials['password']
            );
        } catch(\Throwable $e) {
            return false;
        }

        $token = $this->auth->createSessionCookie(
            $firebaseUser->idToken(),
            60 * 24 * 7 // 1 week
        );

        Session::put('token', $token);
        return true;
    }

    public function logout()
    {
        Session::forget('token');
    }
}
