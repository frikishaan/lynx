<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessClick;
use App\Models\Link;
use App\Models\Scopes\TeamScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class RedirectController
{
    public function __invoke(Request $request, string $shortId)
    {
        $customDomain = $request->get('custom_domain');

        /**  @var \App\Models\Link $link */
        $link = Link::query()
            ->withoutGlobalScope(TeamScope::class)
            ->where('short_id', $shortId)
            ->when(
                $customDomain, 
                function(Builder $query) use ($customDomain) {
                    $query->where('domain_id', $customDomain->id);
                }, 
                function(Builder $query) {
                    $query->whereNull('domain_id');
                }
            )
            ->withCount("choices")
            ->firstOrFail();

        if($link->isExpired()) {
            return abort(404); // TODO: Create a custom 404 page instead
        }

        if($link->isPasswordProtected() || $link->hasChoices())
        {
            return view('link', [ 'link' => $link ]);
        }
        
        ProcessClick::dispatch($link, request()->userAgent(), request()->getClientIp());

        return redirect()->away($link->getRedirectUrl());
    }
}
