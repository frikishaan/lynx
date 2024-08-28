<div>
    <button type="button" wire:click="$refresh">Refresh</button>
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
            <img src="/logo.svg" class="mx-auto w-[100%] h-auto max-w-[80%]">

            <h1 class="text-center font-bold text-3xl mt-8">
                Logoipsum
            </h1>

            <p class="text-center mt-8">
                Lorem ipsum, dolor sit amet consectetur adipisicing elit. Eius maiores commodi enim voluptate amet, eveniet voluptas? Nostrum libero perspiciatis dolorem quo facere possimus blanditiis quidem ratione unde soluta? Asperiores nisi facere eaque!
            </p>

            <div class="max-w-2xl mx-auto flex flex-col md:flex-row gap-4 items-center justify-center p-2 mt-8">
                @foreach ($this->link->choices as $choice)
                    <x-filament::section class="min-w-52">
                        <x-slot name="heading">
                            {{ $choice->title }}
                        </x-slot>

                        <x-slot name="description">
                            This is all the information we hold about the user.
                        </x-slot>
                        
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

    {{-- <x-filament-actions::modals /> --}}
</div>