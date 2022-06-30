<?php

use Glamstack\GoogleWorkspace\Tests\Fakes\Resources\Rest\RestFake;

test('get() - it can use GET to access a single groups', function(){
    $api_client = new RestFake('test');
    $api_client->setUp();
    $response = $api_client->get('https://admin.googleapis.com/admin/directory/v1/groups/' . config('tests.connections.test.test_group_email'));
    expect($response->status->code)->toBe(200);
});

test('get() - it can use GET to list group', function(){
    $api_client = new RestFake('test');
    $api_client->setUp();
    $response = $api_client->get('https://admin.googleapis.com/admin/directory/v1/groups', [
        'maxResults' => 1
    ]);
    expect($response->status->code)->toBe(200);
});

test('get() - it can use GET list groups with a filter and have the same response', function(){
    $api_client = new RestFake('test');
    $api_client->setUp();
    $response = $api_client->get('https://admin.googleapis.com/admin/directory/v1/groups', [
        'query' => 'email=' . config('tests.connections.test.test_group_email')
    ]);
    expect($response->status->code)->toBe(200);
});
