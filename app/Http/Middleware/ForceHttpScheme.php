<?php

namespace Alison\ProjectManagementAssistant\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttpScheme
{
    public function handle(Request $request, Closure $next): Response
    {
        // Довіряти всім проксі для правильного визначення протоколу та хосту
        $request->setTrustedProxies(
            ['*'],
            Request::HEADER_X_FORWARDED_FOR | 
            Request::HEADER_X_FORWARDED_HOST | 
            Request::HEADER_X_FORWARDED_PORT | 
            Request::HEADER_X_FORWARDED_PROTO
        );

        return $next($request);
    }
}
