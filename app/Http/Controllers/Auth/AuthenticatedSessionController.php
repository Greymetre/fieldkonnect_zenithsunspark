<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Models\UserLogin;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $username = $request->input('email');
        $user = User::where('mobile', $username)->orWhere('email', $username)->first();
        if($user){
            if ($user->active != 'Y') {
                return redirect()->back()->with('error', 'Your account is deactivated don\'t hesitate to get in touch with admin.');
            }
            if($user->roles->contains('id', '29')){
                $user['entry_from'] = 'Web';
                $user['provider'] = 'retailers';
                $usersLogin = new UserLogin;
                $usersLogin->save_data($user);
            }
        }
        $request->authenticate();

        session(['user_idsss' => $user->id]);

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $user = auth()->user();
        if($user->roles->contains('id', '29')){
            $user['entry_from'] = 'Web';
            $user['provider'] = 'retailers';
            $usersLogin = new UserLogin;
            $usersLogin->logout($user);
        }
        $request->session()->forget('executive_id');
        
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
