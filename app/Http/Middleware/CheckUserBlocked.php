<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserBlocked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if user is blocked
            if ($user->block == 1) {
                Auth::logout();
                
                // Invalidate the session
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Return appropriate response based on request type
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => __('auth.account_blocked')
                    ], 403);
                }
                
                return redirect()->route('login')
                    ->with('error', __('auth.account_blocked'));
            }
            
            // Check if user is inactive
            if ($user->active == 0) {
                Auth::logout();
                
                // Invalidate the session
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Return appropriate response based on request type
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => __('auth.account_not_activated')
                    ], 403);
                }
                
                return redirect()->route('login')
                    ->with('error', __('auth.account_not_activated'));
            }
        }
        
        return $next($request);
    }
}
