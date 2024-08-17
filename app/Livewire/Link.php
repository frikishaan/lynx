<?php

namespace App\Livewire;

use App\Models\Link as LinkModel;
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
        $link = LinkModel::select('short_id', 'password', 'long_url')
            ->where('short_id', $this->short_id)
            ->firstOrFail();

        if(Hash::check($this->form->getState()['password'], $link->password))
        {
            $this->redirect($link->long_url);
            // dd('true');
        }
        else
        {
            // dd('false');
            dd('error');
        }
        
        // dd($this->short_id);
        // dd($this->form->getState());
    }
    
    public function render()
    {
        return view('livewire.link');
    }
}
