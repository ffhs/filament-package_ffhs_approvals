<?php

it('load actions', function (){
    $response = $this->get('/admin/approvable');
    $response->assertStatus(200);




})->only();

