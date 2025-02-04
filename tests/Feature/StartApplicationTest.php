<?php

it('can startup', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
});
