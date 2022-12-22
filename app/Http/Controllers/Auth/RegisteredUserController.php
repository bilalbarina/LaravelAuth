<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, FirebaseAuth $auth)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email',],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        try {
            $auth->createUser([
                'displayName' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => $request->get('password'),
            ]);
        } catch(\Throwable $e) {
            return back()->withErrors($e->getMessage());
        }
       

        Auth::attempt($validated);

        return redirect(RouteServiceProvider::HOME);
    }
}
