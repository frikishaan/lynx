<div>
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
        
        <form wire:submit="create">
            {{ $this->form }}

            <x-filament::button class="mt-4" type="submit">
                <x-filament::loading-indicator class="h-5 w-5" wire:loading/>
                Submit
            </x-filament::button>
            
        </form>
        
    </x-filament::section>

    <x-filament-actions::modals />
</div>