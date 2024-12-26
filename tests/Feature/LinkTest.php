<?php

use App\Filament\Resources\LinkResource;

beforeEach(function() {
    login();
});

it('it can see all links', function() {
    $response = $this->get(LinkResource::getUrl('index'));

    $response->assertStatus(200);

    $response->assertSeeText('Links');

    $response->assertSeeText('New link');
});