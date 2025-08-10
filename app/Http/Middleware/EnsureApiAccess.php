<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Add API-specific headers
        $response = $next($request);
        
        $response->headers->set('X-API-Version', '1.0');
        $response->headers->set('X-RateLimit-Limit', '100');
        
        return $response;
    }
}
