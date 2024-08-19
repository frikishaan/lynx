<?php

namespace App\Livewire;

use App\Models\Link as LinkModel;
use App\Models\Visit;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Link extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public string $short_id = '';
    
    public function mount(): void
    {
        $this->short_id = request()->route('short_id');
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('password')
                    ->required()
                    ->password()
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $link = LinkModel::where('short_id', $this->short_id)
            ->firstOrFail();

        if(Hash::check($this->form->getState()['password'], $link->password))
        {            
            $link->visits()->save(new Visit([
                'ip' => request()->ip()
            ]));

            $this->redirect($link->getRedirectUrl());
        }
        else
        {
            $this->addError('data.password', 'Incorrect password.');
        }
    }
    
    public function render()
    {
        return view('livewire.link');
    }
}
