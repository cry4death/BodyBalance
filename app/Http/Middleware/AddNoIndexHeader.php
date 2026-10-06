<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds "X-Robots-Tag: noindex" outside production, or always when used as AddNoIndexHeader:always
 * (admin panel).
 */
class AddNoIndexHeader
{
    public function handle(Request $request, Closure $next, ?string $mode = null): Response
    {
        $response = $next($request);

        if ($mode === 'always' || ! app()->isProduction()) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

        return $response;
    }
}
