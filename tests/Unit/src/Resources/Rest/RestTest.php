<?php

use Glamstack\GoogleWorkspace\Tests\Fakes\Resources\Rest\RestFake;

test('get() - it can use GET to list groups', function(){
    $api_client = new RestFake('test');
    $api_client->setUp();
    $response = $api_client->get('https://admin.googleapis.com/admin/directory/v1/groups', [
        'customer' => config('tests.connections.test.customer_id'),
        'domain' => config('tests.connections.test.domain'),
    ]);
    expect($response->status->code)->toBe(200);
});
