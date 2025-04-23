<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('OPTIONS')) {
            $response = response('', 200);
        } else {
            $response = $next($request);
        }
        
        $origin = $request->header('Origin');
        
        if ($origin) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        } else {
            $response->headers->set('Access-Control-Allow-Origin', '*');
        }
        
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-XSRF-TOKEN');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Max-Age', '86400');
        
        return $response;
    }
} 