<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Visit;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function redirect(Request $request, string $short_id)
    {
        $link = Link::where('short_id', $short_id)->firstOrFail();

        $link->visits()->save(new Visit([
            'ip' => $request->ip()
        ]));

        return view('link', [ 'short_id' => $short_id ]);

        return redirect($link->long_url);
    }
}
