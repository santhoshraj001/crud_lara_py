<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;





class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // return redirect()->intended(route('dashboard', absolute: false));  //THIS IS OLD ONE
        return redirect()->intended('/std');             // i added this one after login which page to show
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }



    // its just for authentication check in postman

// public function apiLogin(Request $request)    // this is without token access
// {
//     if (!Auth::attempt($request->only('email', 'password'))) {
//         return response()->json([
//             'status' => false,
//             'message' => 'Invalid credentials'
//         ]);
//     }

//     return response()->json([
//         'status' => true,
//         'message' => 'Login successful',
//         'user' => Auth::user()
//     ]);
// }




public function apiLogin(Request $request)
{
    if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid credentials'
        ]);
    }

    $user = Auth::user();

    // 🔥 Create token
    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'status' => true,
        'message' => 'Login successful',
        'token' => $token
    ]);
}
}
