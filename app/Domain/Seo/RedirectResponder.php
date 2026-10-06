<?php

namespace App\Domain\Seo;

use App\Domain\Seo\Models\Redirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Turns a 404 into a redirect when the admin configured one for this path.
 * Runs only for missing pages, so normal requests never hit the redirects table.
 */
final class RedirectResponder
{
    public function respond(Request $request): ?RedirectResponse
    {
        if (! $request->isMethod('GET') && ! $request->isMethod('HEAD')) {
            return null;
        }

        $redirect = Redirect::query()
            ->where('from_path', Redirect::normalizePath('/'.$request->decodedPath()))
            ->first();

        if (! $redirect) {
            return null;
        }

        $redirect->forceFill(['hits' => $redirect->hits + 1, 'last_hit_at' => now()])->saveQuietly();

        return redirect()->to($this->target($redirect->to_url, $request), $redirect->status_code);
    }

    /**
     * Keeps the original query string (utm tags etc.) unless the target defines its own.
     */
    private function target(string $toUrl, Request $request): string
    {
        $query = $request->getQueryString();

        if ($query === null || str_contains($toUrl, '?')) {
            return $toUrl;
        }

        return $toUrl.'?'.$query;
    }
}
