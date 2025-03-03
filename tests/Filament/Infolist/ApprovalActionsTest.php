<?php

it('load actions', function (){
    $response = $this->get('/admin/approvable');
    $response->assertStatus(200);
})->todo('');

it('load right actions')->todo();
it('overwrite label')->todo();
it('overwrite group label')->todo();
it('overwrite color')->todo();
it('require confirmation')->todo();
it('can have columns')->todo();
