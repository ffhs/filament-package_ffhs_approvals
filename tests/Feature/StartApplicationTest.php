<?php

use App\Models\User;

it('can startup', function () {
    $this
        ->actingAs(User::query()->first())
        ->get('/')
        ->assertStatus(200);
});
