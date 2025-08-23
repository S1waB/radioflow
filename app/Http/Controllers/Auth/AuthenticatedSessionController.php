<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function destroy(Request $request)
    {
        // Handle API token logout if present
        if (method_exists($request->user(), 'currentAccessToken')) {
            $token = $request->user()->currentAccessToken();
            // Only try to delete if it's a personal access token
            if (method_exists($token, 'delete')) {
                $token->delete();
            }
        }

        // Traditional session logout
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Return JSON for API or redirect for web
        return $request->wantsJson()
            ? response()->json(['message' => 'Logged out successfully'])
            : redirect('/');
    }
}