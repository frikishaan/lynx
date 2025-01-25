<?php

use App\Filament\Resources\DomainResource;
use App\Models\Domain;
use Filament\Facades\Filament;

use function Pest\Livewire\livewire;

beforeEach(function() {
    login();
});

it('can render page', function () {
    $this->get(DomainResource::getUrl(name: 'index'))
        ->assertSuccessful();
});

it('it can create with valid domains', function(string $domainName) {

    livewire(DomainResource\Pages\ManageDomains::class)
        ->callAction('create', [
            'name' => $domainName
        ])
        ->assertHasNoErrors();

    $this->assertDatabaseHas('domains', [
        'name' => $domainName
    ]);
})->with([
    'ac.me', 'comp.ly', 'sales.io', 'marketing.io', 'doc.ly'
]);

it('cannot create with invalid domains', function(string $domainName) {

    livewire(DomainResource\Pages\ManageDomains::class)
        ->callAction('create', [
            'name' => $domainName
        ])
        ->assertHasActionErrors([
            'name' => 'The domain name field format is invalid.'
        ]);

})->with([
    'invalid', '123', 'http://', 'abc@gmail.com'
]);

it('cannot create with existing domains', function(string $domainName) {
    Domain::create([
        'name' => $domainName,
        'team_id' => Filament::getTenant()->id
    ]);

    livewire(DomainResource\Pages\ManageDomains::class)
        ->callAction('create', [
            'name' => $domainName
        ])
        ->assertHasActionErrors([
            'name' => 'The domain name already exists.' 
        ]);
})->with([
    'ac.me', 'comp.ly', 'sales.io', 'marketing.io', 'doc.ly'
]);
