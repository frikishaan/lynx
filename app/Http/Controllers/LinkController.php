<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Visit;
use Illuminate\Http\Request;

class LinkController
{
    public function redirect(Request $request, string $shortId)
    {
        /**  @var \App\Models\Link $link */
        $link = Link::where('short_id', $shortId)
            ->withCount("choices")
            ->firstOrFail();

        if($link->isExpired()) {
            return abort(404); // TODO: Create a custom 404 page instead
        }

        if($link->isPasswordProtected() || $link->hasChoices())
        {
            return view('link', [ 'link' => $link ]);
        }
        
        $link->visits()->save(new Visit([
            'ip' => $request->ip()
        ]));

        return redirect()->away($link->getRedirectUrl());
    }
}
