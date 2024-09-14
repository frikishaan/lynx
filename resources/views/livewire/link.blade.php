<div>
    @if($this->showPasswordForm)
        <x-filament::section class="max-w-2xl mx-auto">
            <x-slot name="heading">
                Password
            </x-slot>

            <x-slot name="description">
                This link is password protected. Please enter password to open the destination URL.
            </x-slot>
        
            <x-slot name="icon">
                <x-heroicon-o-lock-closed class="h-6 w-6" />
            </x-slot>
            
            <form wire:submit="validatePassword">
                {{ $this->form }}

                <x-filament::button class="mt-4" type="submit">
                    <x-filament::loading-indicator class="h-5 w-5" wire:loading/>
                    Submit
                </x-filament::button>
                
            </form>
            
        </x-filament::section>
    @elseif($this->showChoices)
        <div class="flex flex-col mx-auto max-w-2xl justify-center p-2 m-2">

            @if($this->link->choice_page_image)
                <img src="{{ Storage::url($this->link->choice_page_image) }}" class="mx-auto w-auto max-h-[400px] h-auto max-w-[80%]">
            @endif

            @if($this->link->choice_page_title)
                <h1 class="text-center font-bold text-3xl mt-8">
                    {{ $this->link->choice_page_title }}
                </h1>
            @endif

            @if($this->link->choice_page_description)
                <p class="text-center mt-8">
                    {{ $this->link->choice_page_description }}
                </p>
            @endif

            <div class="max-w-2xl mx-auto flex flex-col md:flex-row gap-4 justify-center p-2 mt-8">
                @foreach ($this->link->choices()->orderBy('sort_order')->get() as $choice)
                    <x-filament::section class="min-w-52">
                        <x-slot name="heading">
                            {{ $choice->title }}
                        </x-slot>

                        @if($choice->description)
                            <x-slot name="description">
                                {{ $choice->description }}
                            </x-slot>
                        @endif

                        <x-filament::button
                            wire:click.prevent="visit({{ $choice->id }})"
                            tag="a"
                            size="xs"
                            icon="heroicon-o-arrow-top-right-on-square"
                            icon-position="after"
                            rel="noopener"
                            class="cursor-pointer"
                        >
                            Open link
                        </x-filament::button>
                    </x-filament::section>
                @endforeach
            </div>
        </div>
    @endif
</div>
