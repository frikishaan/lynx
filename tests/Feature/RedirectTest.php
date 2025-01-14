<?php

use App\Models\Domain;
use App\Models\Link;
use App\Models\LinkChoice;
use Filament\Facades\Filament;

use function Pest\Livewire\livewire;

it('can redirect to original url', function () {

    login();

    /** @var App\Models\Link */
    $link = Link::create([
        'long_url' => 'https://laravel.com',
        'team_id' => Filament::getTenant()->id
    ]);

    $this->get($link->getShortURL())
        ->assertRedirect($link->long_url);

});

it('prompts for password if link is password protected', function () {

    $team = createTeam();

    /** @var App\Models\Link */
    $link = Link::create([
        'long_url' => 'https://laravel.com',
        'password' => 'password',
        'team_id' => $team->id
    ]);

    $this->get($link->getShortURL())
        ->assertSeeText('Password');

});

it('shows error if password is incorrect', function () {
    $team = createTeam();

    /** @var App\Models\Link */
    $link = Link::create([
        'long_url' => 'https://laravel.com',
        'password' => 'password',
        'team_id' => $team->id
    ]);

    livewire('link', ['link' => $link])
        ->set('data.password', 'secret')
        ->call('validatePassword')
        ->assertHasErrors(['data.password']);
});

it('redirects to original url if password is correct', function () {
    $team = createTeam();

    /** @var App\Models\Link */
    $link = Link::create([
        'long_url' => 'https://laravel.com',
        'password' => 'password',
        'team_id' => $team->id
    ]);

    livewire('link', ['link' => $link])
        ->set('data.password', 'password')
        ->call('validatePassword')
        ->assertRedirect($link->long_url);
});

it('appends utm params to original url if available', function() {

    $team = createTeam();

    /** @var App\Models\Link */
    $link = Link::create([
        'long_url' => 'https://laravel.com',
        'has_utm_params' => 1,
        'utm_source' => 'source',
        'utm_medium' => 'medium',
        'utm_campaign' => 'campaign',
        'utm_term' => 'term',
        'utm_content' => 'content',
        'team_id' => $team->id
    ]);

    $this->get($link->getShortURL())
        ->assertRedirect($link->long_url . '?utm_source=source&utm_medium=medium&utm_content=content&utm_term=term&utm_campaign=campaign');

});

it('redirects to choice page if choices are available', function() {

    $team = createTeam();

    /** @var App\Models\Link */
    $link = Link::create([
        'long_url' => 'https://google.com',
        'team_id' => $team->id,
        'choice_page_title'=> 'Choose a search engine',
        'choice_page_description' => 'Please choose a search engine to continue.'
    ]);

    $link->choices()->saveMany([
        new LinkChoice([
            'title' => 'Google',
            'destination_url' => 'https://google.com'
        ]),
        new LinkChoice([
            'title' => 'Bing',
            'destination_url' => 'https://bing.com'
        ]),
        new LinkChoice([
            'title' => 'Duckduckgo',
            'destination_url' => 'https://duckduckgo.com'
        ])
    ]);

    $this->get($link->getShortURL())
        ->assertSeeText([
            'Choose a search engine', 'Please choose a search engine to continue.',
            'Google', 'Bing', 'Duckduckgo'
        ]);

});

it('redirects to original url if choice is correct', function() {

    $team = createTeam();

    /** @var App\Models\Link */
    $link = Link::create([
        'long_url' => 'https://laravel.com',
        'team_id' => $team->id
    ]);

    $choice = $link->choices()->create([
        'title' => 'Google',
        'destination_url' => 'https://google.com'
    ]);

    livewire('link', ['link' => $link])
        ->call('visit', $choice->id)
        ->assertRedirect($choice->destination_url);

});

it('return 404 if link is expired', function () {

    $team = createTeam();

    /** @var App\Models\Link */
    $link = Link::create([
        'long_url' => 'https://laravel.com',
        'expires_at' => now()->subDay(),
        'team_id' => $team->id
    ]);

    $this->get($link->getShortURL())
        ->assertNotFound();

});

it('returns 404 if link is accessed from a different domain', function () {

    $team = createTeam();

    $domain = Domain::create([
        'name' => 'lynx-temp.test',
        'team_id' => $team->id
    ]);

    /** @var App\Models\Link */
    $link = Link::create([
        'long_url' => 'https://laravel.com',
        'team_id' => $team->id,
        'domain_id' => $domain->id
    ]);

    $this->get('/' . $link->short_id)
        ->assertNotFound();
});

test('same short code returns different urls for different domains', function () {

    $team = createTeam();

    $domain1 = Domain::create([
        'name' => 'lynx-temp1.test',
        'team_id' => $team->id
    ]);

    $domain2 = Domain::create([
        'name' => 'lynx-temp2.test',
        'team_id' => $team->id
    ]);

    /** @var App\Models\Link */
    $link1 = Link::create([
        'long_url' => 'https://laravel.com',
        'short_id' => 'test',
        'team_id' => $team->id,
        'domain_id' => $domain1->id
    ]);
    
    /** @var App\Models\Link */
    $link2 = Link::create([
        'long_url' => 'https://github.com',
        'short_id' => 'test',
        'team_id' => $team->id,
        'domain_id' => $domain2->id
    ]);

    $this->get($link1->getShortURL())
        ->assertRedirect($link1->long_url);

    $this->get($link2->getShortURL())
        ->assertRedirect($link2->long_url);
});