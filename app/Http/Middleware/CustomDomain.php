<?php

namespace App\Http\Middleware;

use App\Models\Domain;
use App\Models\Scopes\TeamScope;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        if ($host !== app_domain()) {
            $customDomain = Domain::withoutGlobalScope(TeamScope::class)
                ->where('name', $host)->first();

            if (!$customDomain) {
                abort(404);
            }

            $request->merge(['custom_domain' => $customDomain]);
        }

        return $next($request);
    }
}
