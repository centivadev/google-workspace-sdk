<?php

namespace Glamstack\GoogleWorkspace\Tests\Unit\src\Models\Resources\Directory;


use Glamstack\GoogleWorkspace\Resources\Directory\Directory;

test('verifyConfigArray() - it requires api_scopes to be set', function(){
    $directory_client = new Directory(null,[
        'customer_id' => 'fake_id',
        'domain' => 'fake_domain',
        'json_key_file_path' => storage_path('a_file_path')
    ]);
})->expectExceptionMessage('The api scopes field is required.');

test('verifyConfigArray() - it requires customer_id to be set', function(){
    $directory_client = new Directory(null,[
        'api_scopes' => ['fake_scope_1'],
        'domain' => 'fake_domain',
        'json_key_file_path' => storage_path('a_file_path')
    ]);
})->expectExceptionMessage('The customer id field is required.');

test('verifyConfigArray() - it requires domain to be set', function(){
    $directory_client = new Directory(null,[
        'api_scopes' => ['fake_scope_1'],
        'customer_id' => 'fake_id',
        'json_key_file_path' => storage_path('a_file_path')
    ]);
})->expectExceptionMessage('The domain field is required.');

test('verifyConfigArray() - it requires json_key or json_key_file_path to be set', function(){
    $directory_client = new Directory(null,[
        'api_scopes' => ['fake_scope_1'],
        'domain' => 'fake_domain',
        'customer_id' => 'fake_id',
    ]);
})->expectExceptionMessage('Either the json_key_file_path or json_key parameters are required');

test('verifyConfigArray() - it will set the config array properly', function(){
    $directory_client = new Directory(null,[
        'api_scopes' => ['fake_scope_1'],
        'domain' => 'fake_domain',
        'customer_id' => 'fake_id',
        'json_key_file_path' => storage_path('a_file_path')
    ]);
    expect($directory_client->connection_config['domain'])->toBe('fake_domain');
    expect($directory_client->connection_config['api_scopes'])->toBe(['fake_scope_1']);
    expect($directory_client->connection_config['customer_id'])->toBe('fake_id');
    expect($directory_client->connection_config['json_key_file_path'])->toBe(storage_path('a_file_path'));
});
