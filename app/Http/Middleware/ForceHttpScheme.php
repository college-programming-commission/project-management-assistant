<?php

namespace Alison\ProjectManagementAssistant\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttpScheme
{
    public function handle(Request $request, Closure $next): Response
    {
        // Примусово встановити HTTP схему для всіх запитів
        $request->server->set('HTTPS', 'off');
        $request->server->set('HTTP_X_FORWARDED_PROTO', 'http');
        $request->server->set('HTTP_X_FORWARDED_SSL', 'off');
        $request->server->remove('HTTPS');
        
        // Встановити схему запиту на HTTP
        $request->setTrustedProxies(['0.0.0.0/0'], Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO);

        return $next($request);
    }
}
