<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SsoController extends Controller
{
    /**
     * Redirect the user to the Keycloak authentication page.
     */
    public function redirectToProvider(Request $request)
    {
        $request->validate([
            'tahun' => 'required|string',
        ]);

        // Put tahun in session so we can retrieve it in callback
        session(['sso_tahun' => $request->tahun]);

        \Illuminate\Support\Facades\Log::info('SSO Redirecting', ['config' => config('services.keycloak')]);

        return Socialite::driver('keycloak')->redirect();
    }

    /**
     * Obtain the user information from Keycloak.
     */
    public function handleProviderCallback()
    {
        try {
            // Standard Socialite user retrieval
            $socialUser = Socialite::driver('keycloak')->user();
            
            \Illuminate\Support\Facades\Log::info('SSO Callback Data Received', ['email' => $socialUser->getEmail()]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SSO Callback Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Authentication failed.');
        }

        $user = User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            \Illuminate\Support\Facades\Log::info('Creating new SSO user', ['email' => $socialUser->getEmail()]);
            $user = User::create([
                'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? $socialUser->getEmail(),
                'email' => $socialUser->getEmail(),
                'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(24)),
            ]);

            try {
                $user->assignRole('guest');
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to assign role guest: ' . $e->getMessage());
            }
        }

        \Illuminate\Support\Facades\Auth::login($user, true);
        \Illuminate\Support\Facades\Log::info('User logged in via SSO', ['user_id' => $user->id]);

        $tahun = session('sso_tahun', date('Y'));
        session(['tahun' => $tahun]);
        session()->forget('sso_tahun');

        request()->session()->save();
        \Illuminate\Support\Facades\Log::info('Session saved, redirecting to dashboard');

        return redirect()->intended(route('dashboard'));
    }
}
