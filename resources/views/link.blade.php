<x-guest-layout 
    :darkModeEnabled="$link->enable_dark_mode" 
    :title="$link->choice_page_title"
>
    @livewire('link', ['link' => $link])
</x-guest-layout>
