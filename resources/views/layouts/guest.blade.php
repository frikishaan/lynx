<!DOCTYPE html>
<html 
    lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
    class="@if($darkModeEnabled) dark @endif"
>
<head>
    <meta charset="utf-8">
    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} - {{ config('app.name', 'Lynx') }}</title>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @filamentStyles
    @vite('resources/css/app.css')

    <!-- Scripts -->
    @vite('resources/js/app.js')
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-950 dark:text-white">

    <div class="min-h-screen flex flex-col">
        
        <!-- Main Content -->
        <main class="flex-grow">
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 flex items-center justify-center">
                {{ $slot }}
            </div>
        </main>

        @if(config('lynx.render_lynx_footer'))
            <footer>
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <p class="text-center text-gray-500 text-sm">
                        Powered by Lynx
                    </p>
                </div>
            </footer>
        @endif
    </div>
        

    @filamentScripts
    @vite('resources/js/app.js')
</body>
</html>