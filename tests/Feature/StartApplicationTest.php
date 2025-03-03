<?php

it('can startup', function () {
    $this->actingAs(\App\Models\User::query()->first());
    $response = $this->get('/');
    $response->assertStatus(200);
});
