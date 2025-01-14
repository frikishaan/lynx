<?php

use App\Filament\Resources\LinkResource;
use App\Models\Link;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;

use function Pest\Livewire\livewire;

beforeEach(function() {
    login();
});

it('can see all links', function() {

    $links = Link::factory(10)->create([
        'team_id' => Filament::getTenant()->id
    ]);

    $response = $this->get(LinkResource::getUrl('index'));
    
    $response->assertStatus(200);

    $response->assertSeeText('Links');

    $response->assertSeeText('New link');

    livewire(LinkResource\Pages\ListLinks::class)
        ->assertCanSeeTableRecords($links);

});

it('can create new links', function () {
    $this->get(LinkResource::getUrl('create'))->assertSuccessful();

    $link = Link::factory()->make();
 
    livewire(LinkResource\Pages\CreateLink::class)
        ->fillForm([
            'long_url' => $link->long_url,
            'title' => $link->title
        ])
        ->call('create')
        ->assertHasNoFormErrors();
 
    $this->assertDatabaseHas(Link::class, [
        'long_url' => $link->long_url,
        'title' => $link->title
    ]);
});

it('can validate input', function () {

    $link = Link::factory()->create([
        'short_id' => 'test',
        'team_id' => Filament::getTenant()->id
    ]);

    livewire(LinkResource\Pages\CreateLink::class)
        ->fillForm([
            'long_url' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['long_url' => 'required'])
        ->fillForm([
            'long_url' => fake()->url(),
            'short_id' => $link->short_id
        ])
        ->call('create')
        ->assertHasFormErrors(['short_id']);
});

it('can update links', function () {

    $link = Link::factory()->create([
        'team_id' => Filament::getTenant()->id
    ]);

    livewire(LinkResource\Pages\EditLink::class, [
        'record' => $link->getRouteKey()
    ])
        ->fillForm([
            'title' => 'Updated title'
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($link->refresh())
        ->title->toBe('Updated title');

});

it('can delete link', function () {

    $link = Link::factory()->create([   
        'team_id' => Filament::getTenant()->id
    ]);

    livewire(LinkResource\Pages\EditLink::class, [
        'record' => $link->getRouteKey()
    ])
        ->callAction(DeleteAction::class);

    $this->assertModelMissing($link);
});

it('can search links', function () {

    $link = Link::factory()->create([
        'title' => 'Seachable title',
        'team_id' => Filament::getTenant()->id
    ]);

    livewire(LinkResource\Pages\ListLinks::class)
        ->searchTable($link->title)
        ->assertSee($link->title)
        ->searchTable('random')
        ->assertDontSee($link->title);
});

it('can see expired links using filter', function () {

    $expiredLink = Link::factory()->create([
        'expires_at' => now()->subDay(),
        'team_id' => Filament::getTenant()->id
    ]);
 
    livewire(LinkResource\Pages\ListLinks::class)
        ->assertCanNotSeeTableRecords(collect($expiredLink))
        ->filterTable('expired')
        ->assertSee($expiredLink->title);

});

it('can open link using action', function() {
    
    $link = Link::factory()->create([
        'team_id' => Filament::getTenant()->id
    ]);

    livewire(LinkResource\Pages\EditLink::class, [
        'record' => $link->getRouteKey()
    ])
        ->assertActionEnabled('open_link')
        ->assertActionHasLabel('open_link', 'Open link')
        ->assertActionHasIcon('open_link', 'heroicon-o-arrow-top-right-on-square')
        ->assertActionHasUrl('open_link', 'http://' . $link->getShortURL())
        ->assertActionShouldOpenUrlInNewTab('open_link');

});

it('can see the QR code')->todo();
