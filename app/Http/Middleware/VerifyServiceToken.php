<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyServiceToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('catalog.service_token');
        $provided = (string) $request->header('X-Service-Token', '');

        if ($provided === '') {
            $provided = (string) ($request->server('HTTP_X_SERVICE_TOKEN')
                ?: $request->server('REDIRECT_HTTP_X_SERVICE_TOKEN')
                ?: '');
        }

        if ($expected === '' || ! hash_equals($expected, $provided)) {
            return response()->json([
                'message' => 'Token de servicio inválido',
            ], 401);
        }

        return $next($request);
    }
}
