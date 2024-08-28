<?php

namespace App\Livewire;

use App\Models\Link as LinkModel;
use App\Models\Visit;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Link extends Component implements HasForms
{
    use InteractsWithForms;

    private LinkModel $link;

    #[Locked]
    public string $shortId;

    public ?array $data = [];

    private bool $showPasswordForm = false;
    
    private bool $showChoices = false;

    public function boot(): void
    {
        $this->link = LinkModel::where('short_id', $this->shortId)
            ->firstOrFail();

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
                $this->link->visits()->save(new Visit([
                    'ip' => request()->ip()
                ]));
    
                // $this->redirect($this->link->getRedirectUrl());
                redirect()->away($this->link->getRedirectUrl());
            }
        }
        else
        {
            $this->form->fill();
            $this->addError('data.password', 'Incorrect password.');
        }
    }

    private function getLinkChoices(): mixed
    {
        return $this->link->choices();
    }

    public function visit(int $choiceId)
    {
        $this->link->visits()->save(new Visit([
            'ip' => request()->ip(),
            'choice_id' => $choiceId
        ]));
    }
    
    public function render(): View
    {
        return view('livewire.link', [
            'showPasswordForm' => $this->showPasswordForm,
            'showChoices' => $this->showChoices
        ]);
    }
}
