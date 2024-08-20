<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Visit;
use Illuminate\Http\Request;

class LinkController
{
    public function redirect(Request $request, string $short_id)
    {
        $link = Link::where('short_id', $short_id)->firstOrFail();

        if($link->isExpired()) {
            return abort(404); // TODO: Create a custom 404 page instead
        }

        if($link->password)
        {
            return view('link', [ 'short_id' => $short_id ]);
        }
        
        $link->visits()->save(new Visit([
            'ip' => $request->ip()
        ]));

        return redirect()->away($link->getRedirectUrl());
    }
}
