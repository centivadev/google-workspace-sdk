<?php

use Glamstack\GoogleWorkspace\Tests\Fakes\Resources\Rest\RestFake;

test('getRequest() - it can send HTTP GET request', function(){
    $api_client = new RestFake('test');
    $api_client->setUp();
    $response = $api_client->get('https://admin.googleapis.com/admin/directory/v1/groups');
    expect($response->status->ok)->toBeTrue();
});

test('postRequest() - it can send HTTP POST request', function(){
    $api_client = new RestFake('test');
    $api_client->setUp();
    $response = $api_client->post('https://admin.googleapis.com/admin/directory/v1/groups',[
        'email' => 'glamstack_test_post_' . config('tests.connections.test.test_group_email')
    ]);
    expect($response->status->code)->toBe(200);
    expect($response->object->email)->toBe('glamstack_test_post_' . config('tests.connections.test.test_group_email'));
    $api_client->delete('https://admin.googleapis.com/admin/directory/v1/groups/' . $response->object->id);
});

test('putRequest() - it can send HTTP PUT request', function(){
    $api_client = new RestFake('test');
    $api_client->setUp();
    $response = $api_client->post('https://admin.googleapis.com/admin/directory/v1/groups',[
        'email' => 'glamstack_test_put_' . config('tests.connections.test.test_group_email')
    ]);

    $put_response = $api_client->put(
        'https://admin.googleapis.com/admin/directory/v1/groups/' . $response->object->id,
        [
            'name' => 'glamstack-testing-put'
        ]
    );

    expect($put_response->object->name)->toBe('glamstack-testing-put');
    $api_client->delete('https://admin.googleapis.com/admin/directory/v1/groups/' . $response->object->id);

});

test('deleteRequest() - it can send HTTP DELETE request', function(){
    $api_client = new RestFake('test');
    $api_client->setUp();
    $response = $api_client->post('https://admin.googleapis.com/admin/directory/v1/groups',[
        'email' => 'glamstack_test_delete_' . config('tests.connections.test.test_group_email')
    ]);
    $delete_response = $api_client->delete('https://admin.googleapis.com/admin/directory/v1/groups/' . $response->object->id);
    expect($delete_response->status->successful)->toBeTrue();
    expect($delete_response->status->code)->toBe(204);
});
