<?php

it('displays the feed page', function () {
    $this->get('/feed')
        ->assertSuccessful()
        ->assertSee('Feed Promo');
});
