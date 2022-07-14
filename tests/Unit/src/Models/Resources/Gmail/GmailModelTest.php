<?php

namespace Glamstack\GoogleWorkspace\Tests\Unit\src\Models\Resources\Gmail;

use Glamstack\GoogleWorkspace\Resources\Gmail\Gmail;

test('verifyConfigArray() - it requires api_scopes to be set', function(){
    $directory_client = new Gmail(null,[
        'json_key_file_path' => storage_path('a_file_path')
    ]);
})->expectExceptionMessage('The api scopes field is required.');

test('verifyConfigArray() - it requires json_key or json_key_file_path to be set', function(){
    $directory_client = new Gmail(null,[
        'api_scopes' => ['fake_scope_1'],
    ]);
})->expectExceptionMessage('Either the json_key_file_path or json_key parameters are required');

test('verifyConfigArray() - it will set the config array properly', function(){
    $directory_client = new Gmail(null,[
        'api_scopes' => ['fake_scope_1'],
        'json_key_file_path' => storage_path('a_file_path')
    ]);
    expect($directory_client->connection_config['api_scopes'])->toBe(['fake_scope_1']);
    expect($directory_client->connection_config['json_key_file_path'])->toBe(storage_path('a_file_path'));
});
