<?php

test('home page redirects to login', function () {

    $response = $this->get('/');

    $response->assertRedirect($uri = '/app/login');
});

it('can see login form', function () {
    $response = $this->get('app/login');

    $response->assertSeeText([
        'Sign in', 'Email address', 'Password', 'Remember me'
    ]);
});

it('can login to panel', function() {
    login();

    $this->assertAuthenticated();

    $this->get('/app/1')
        ->assertSuccessful()
        ->assertSeeText('Dashboard');
});