<?php

if(! function_exists('app_domain'))
{
    function app_domain(): string
    {
        return str(config('app.url'))
            ->after('://')
            ->value;
    }
}
