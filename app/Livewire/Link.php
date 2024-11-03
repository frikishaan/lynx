<?php

namespace App\Livewire;

use App\Jobs\ProcessClick;
use App\Models\Link as LinkModel;
use App\Models\LinkChoice;
use App\Models\Visit;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Link extends Component implements HasForms
{
    use InteractsWithForms;

    public LinkModel $link;

    public ?array $data = [];

    private bool $showPasswordForm = false;
    
    private bool $showChoices = false;

    public function boot(): void
    {
        if($this->link->isPasswordProtected())
        {
            $this->showPasswordForm = true;
        }
        else if($this->link->hasChoices())
        {
            $this->showChoices = true;
        }
    }
    
    public function mount(): void
    {
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

    public function validatePassword(): void
    {
        if(Hash::check($this->form->getState()['password'], $this->link->password))
        {
            if($this->link->hasChoices())
            {
                $this->showPasswordForm = false;
                $this->showChoices = true;
                $this->link->load('choices');
            }
            else {
                ProcessClick::dispatch($this->link, request()->userAgent(), request()->getClientIp());
                
                redirect()->away($this->link->getRedirectUrl());
            }
        }
        else
        {
            $this->form->fill();
            $this->addError('data.password', 'Incorrect password.');
        }
    }

    public function visit(int $choiceId)
    {       
        /** @var App\Models\LinkChoice */
        $choice = $this->link->choices()->where('id', $choiceId)->first();

        ProcessClick::dispatch($this->link, request()->userAgent(), request()->getClientIp(), $choiceId);

        redirect()->away($choice->destination_url);
    }
    
    public function render(): View
    {
        return view('livewire.link', [
            'showPasswordForm' => $this->showPasswordForm,
            'showChoices' => $this->showChoices
        ]);
    }
}
