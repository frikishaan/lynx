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
    
    'render_lynx_footer' => true,

    /*
    |--------------------------------------------------------------------------
    | Application UI Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the timezone for your application UI.
    | Date and time will displayed on the UI in this timezone.
    |
    */

    'timezone' => 'Asia/Kolkata'

];
