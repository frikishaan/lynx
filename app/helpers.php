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

function getColors(): array {
    
    return [
        'rgb(254, 164, 127)', 'rgb(37, 204, 247)', 'rgb(234, 181, 67)',
        'rgb(85, 230, 193)', 'rgb(202, 211, 200)', 'rgb(249, 127, 81)',
        'rgb(27, 156, 252)', 'rgb(248, 239, 186)', 'rgb(88, 177, 159)',
        'rgb(44, 58, 71)', 'rgb(179, 55, 113)', 'rgb(59, 59, 152)',
        'rgb(253, 114, 114)', 'rgb(154, 236, 219)', 'rgb(214, 162, 232)',
        'rgb(109, 33, 79)', 'rgb(24, 44, 97)', 'rgb(252, 66, 123)',
        'rgb(189, 197, 129)', 'rgb(130, 88, 159)'
    ];
}
