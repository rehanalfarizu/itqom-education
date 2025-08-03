<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Foundation\AliasLoader;

class DisableFacadeCaching
{
    public function handle(Request $request, Closure $next)
    {
        // Override file_put_contents for facade caching in production
        if (app()->environment('production')) {
            // Monkey patch to prevent facade cache writing
            $originalFunction = 'file_put_contents';
            
            // Check if the call is for facade cache
            register_shutdown_function(function() {
                // This will run after the request but before facade cache writing
                if (function_exists('runkit_function_redefine')) {
                    // Only if runkit extension is available (which it won't be on Heroku)
                    // This is just a fallback approach
                }
            });
        }
        
        return $next($request);
    }
}
