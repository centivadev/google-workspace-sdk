<?php

use Glamstack\GoogleWorkspace\Tests\Fakes\ApiClientFake;

it('throws exception if api_scopes is not an array', function(){
    $client = new ApiClientFake(null, [
        'api_scopes' => 'testing.api.scopes',
        'customer_id' => 'testing_id',
        'domain' => 'testing-domain',
        'json_key_file_path' => storage_path('not_real')
    ]);
})->expectExceptionMessage('The api scopes must be an array.');

it('throws exception if customer_id is not a string', function(){
    $client = new ApiClientFake(null, [
        'api_scopes' => ['testing.api.scopes'],
        'customer_id' => ['testing_id_array'],
        'domain' => 'testing-domain',
        'json_key_file_path' => storage_path('not real')
    ]);
})->expectExceptionMessage('The customer id must be a string');

it('throws exception if domain is not a string', function(){
   $client = new ApiClientFake(null, [
       'api_scopes' => ['testing.api.scopes'],
       'customer_id' => 'testing_id',
       'domain' => ['testing-domain', 'testing-domain-2'],
       'json_key_file_path' => storage_path('not real')
   ]);
})->expectExceptionMessage('The domain must be a string');

it('throws exception if subject_email is not string', function(){
    $client = new ApiClientFake(null, [
        'api_scopes' => ['testing.api.scopes'],
        'customer_id' => 'testing_id',
        'domain' => 'testing-domain',
        'json_key_file_path' => storage_path('a_file_path'),
        'subject_email' => ['subject_email@example.com', 'another_subject_email@example.com']
    ]);
})->expectExceptionMessage('The subject email must be a string');


it('throws exception if json_key_file_path is not a string', function(){
    $client = new ApiClientFake(null, [
        'api_scopes' => ['testing.api.scopes'],
        'customer_id' => 'testing_id',
        'domain' => 'testing-domain',
        'json_key_file_path' => [storage_path('a_file_path'), storage_path('second_file_path')],
    ]);
})->expectExceptionMessage('The json key file path must be a string');

it('throws exception if json_key is not a string', function(){
    $client = new ApiClientFake(null, [
       'api_scopes' => ['testing.api.scopes'],
       'customer_id' => 'testing_id',
        'domain' => 'testing-domain',
       'json_key' => ['fake json_key example', 'fake json_key example 2']
   ]);
})->expectExceptionMessage('The json key must be a string');

it('throws exception if log_channels is not an array', function(){
    $client = new ApiClientFake(null, [
        'api_scopes' => ['testing.api.scopes'],
        'customer_id' => 'testing_id',
        'domain' => 'testing-domain',
        'json_key_file_path' => storage_path('a_file_path'),
        'log_channels' => 'single'
    ]);
})->expectExceptionMessage('The log channels must be an array');

it('throws exception if neither json_key_file_path or json_key are set', function(){
    $client = new ApiClientFake(null, [
        'api_scopes' => ['testing.api.scopes'],
        'customer_id' => 'testing_id',
        'domain' => 'testing-domain',
    ]);
})->expectExceptionMessage('Either the json_key_file_path or json_key parameters are required');

it('can set the connection_key', function(){
    $client = new ApiClientFake('test');
    $client->setConnectionKey('testing');
    expect($client->connection_key)->toBe('testing');
});

it('can set the connection_config with json_key', function(){
    $client = new ApiClientFake(null, [
        'api_scopes' => ['testing.api.scopes'],
        'customer_id' => 'testing_id',
        'domain' => 'testing-domain',
        'json_key' => 'example testing key'
    ]);
    expect($client->connection_config['json_key'])->toBe('example testing key');
});

it('can set the connection_config with json_key_file_path', function(){
    $client = new ApiClientFake(null, [
        'api_scopes' => ['testing.api.scopes'],
        'customer_id' => 'testing_id',
        'domain' => 'testing-domain',
        'json_key_file_path' => storage_path('a_file_path'),
    ]);
    expect($client->connection_config['json_key_file_path'])->toBe(storage_path('a_file_path'));
});

it('will set connection key if not provided', function(){
   $client = new ApiClientFake();
   expect($client->connection_key)->toBe('test');
});

//test('getConfigArrayApiScopes() - it will throw error if api scopes is not set', function(){
//    $api_scopes = null;
//    new ApiClientFake(null, [
//        'api_scopes' => $api_scopes,
//        'customer_id' => config('tests.connections.test.customer_id'),
//        'domain' => config('tests.connections.test.domain'),
//        'json_key_file_path' => storage_path('keys/glamstack-google-workspace/test.json'),
//        'log_channels' => ['single'],
//        'subject_email' => config('tests.connections.test.subject_email')
//    ]);
//})->expectExceptionMessage('The api scopes field is required.');

//
//test('getConfigArrayApiScopes() - it will throw error if api scopes is not set', function(){
//    $api_scopes = null;
//    new ApiClientFake(null, [
//        'api_scopes' => $api_scopes,
//        'customer_id' => config('tests.connections.test.customer_id'),
//        'domain' => config('tests.connections.test.domain'),
//        'json_key_file_path' => storage_path('keys/glamstack-google-workspace/test.json'),
//        'log_channels' => ['single'],
//        'subject_email' => config('tests.connections.test.subject_email')
//    ]);
//})->expectExceptionMessage('The api scopes field is required.');
