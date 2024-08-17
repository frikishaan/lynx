<?php

namespace App\Providers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        DatePicker::configureUsing(function(DatePicker $datePicker) {
            $datePicker->displayFormat('d/m/Y');
        });

        DateTimePicker::configureUsing(function(DateTimePicker $datePicker) {
            $datePicker->displayFormat('d/m/Y H:m');
        });

        Field::macro('loadingIndicator', function(string $target){
            $this->
                hint(new HtmlString(
                    Blade::render('<x-filament::loading-indicator class="h-5 w-5" wire:loading wire:target="data.'. $target .'" />')
                ));

            return $this;
        });
    }
}
