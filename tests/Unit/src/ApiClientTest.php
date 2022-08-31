<?php

use Glamstack\GoogleWorkspace\Tests\Fakes\ApiClientFake;



test('setConnectionKey() - it can set the connection_key', function(){
    $client = new ApiClientFake('test');
    $client->setConnectionKey('testing');
    expect($client->connection_key)->toBe('testing');
});

test('construct() - it can set the connection_config with json_key', function(){
    $client = new ApiClientFake(null, [
        'api_scopes' => ['testing.api.scopes'],
        'customer_id' => 'testing_id',
        'domain' => 'testing-domain',
        'json_key' => 'example testing key'
    ]);
    expect($client->connection_config['json_key'])->toBe('example testing key');
});

test('construct() - it can set the connection_config with json_key_file_path', function(){
    $client = new ApiClientFake(null, [
        'api_scopes' => ['testing.api.scopes'],
        'customer_id' => 'testing_id',
        'domain' => 'testing-domain',
        'json_key_file_path' => storage_path('a_file_path'),
    ]);
    expect($client->connection_config['json_key_file_path'])->toBe(storage_path('a_file_path'));
});

test('construct() - it will set connection key if not provided', function(){
   $client = new ApiClientFake();
   expect($client->connection_key)->toBe('test');
});

