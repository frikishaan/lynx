<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessClick;
use App\Models\Link;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class LinkController
{
    public function __invoke(Request $request, string $shortId)
    {
        $customDomain = $request->get('custom_domain');

        /**  @var \App\Models\Link $link */
        $link = Link::where('short_id', $shortId)
            ->when($customDomain, function(Builder $query) use ($customDomain) {
                $query->where('domain_id', $customDomain->id);
            })
            ->when(! $customDomain, function(Builder $query) {
                $query->whereNull('domain_id');
            })
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
