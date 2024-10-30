<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Characters allowed in the short link
    |--------------------------------------------------------------------------
    |
    | Here you may specify the characters that can be used while generating
    | unique IDs of short url.
    |
    */

    'allowed_characters' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
    
    'domain' => env('APP_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Length of the Short Id
    |--------------------------------------------------------------------------
    |
    | Here you may specify the length of the generated short url unique ID.
    | Should be long enough to prevent collision.
    |
    | See - https://zelark.github.io/nano-id-cc/ 
    |
    */

    'short_id_size' => 8,
    
    /*
    |--------------------------------------------------------------------------
    | Show 'Powered By Lynx' text in the footer
    |--------------------------------------------------------------------------
    |
    | Here you may specify whether you want to show the footer text at the 
    | bottom of the page.
    |
    */
    'render_lynx_footer' => true,

    /*
    |--------------------------------------------------------------------------
    | Application UI Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the timezone for your application UI.
    | Date and time will displayed on the UI in this timezone.
    | See - https://www.php.net/manual/en/timezones.php
    |
    */

    'timezone' => 'Asia/Kolkata',

    /*
    |--------------------------------------------------------------------------
    | Geo Location Action
    |--------------------------------------------------------------------------
    |
    | This is a action which Lynx use to fetch the geo location data from IP
    | address. Currently we have a built-in action which used 
    | FreeIPAPI (https://freeipapi.com/). However, you can create your own 
    | action which extends the \App\Abstracts\BaseLocationAction class.
    |
    */
    'location_action' => \App\Actions\FreeIPAPILocationAction::class

];
