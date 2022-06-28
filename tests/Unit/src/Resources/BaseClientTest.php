<?php

use Glamstack\GoogleWorkspace\Tests\Fakes\ApiClientFake;
use Glamstack\GoogleWorkspace\Tests\Fakes\Resources\Rest\MethodFake;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

test('setDomain() - it can set the domain from connection key', function(){
    $api_client = new ApiClientFake('test');
    $method_client = new MethodFake($api_client);
    $method_client->setUp();
    expect($method_client->getDomain())->toBe(config('tests.connections.test.domain'));
});

test('setDomain() - it can set the domain from connection config array', function(){
    $api_client = new ApiClientFake(null, [
        'api_scopes' => [
            'https://www.googleapis.com/auth/admin.directory.group',
            'https://www.googleapis.com/auth/contacts'
        ],
        'customer_id' => config('tests.connections.test.customer_id'),
        'domain' => config('tests.connections.test.domain'),
        'json_key_file_path' => storage_path('keys/glamstack-google-workspace/test.json'),
        'log_channels' => ['single'],
        'subject_email' => config('tests.connections.test.subject_email')
    ]);
    $method_client = new MethodFake($api_client);
    $method_client->setUp();
    expect($method_client->getDomain())->toBe(config('tests.connections.test.domain'));
});

test('setCustomerId() - it can set a customer ID from a connection key', function(){
    $api_client = new ApiClientFake('test');
    $method_client = new MethodFake($api_client);
    $method_client->setUp();
    expect($method_client->getCustomerId())->toBe(config('tests.connections.test.customer_id'));
});

test('setCustomerId() - it can set a customer ID from a connection config array', function(){
    $api_client = new ApiClientFake(null, [
       'api_scopes' => [
            'https://www.googleapis.com/auth/admin.directory.group',
            'https://www.googleapis.com/auth/contacts'
        ],
        'customer_id' => config('tests.connections.test.customer_id'),
        'domain' => config('tests.connections.test.domain'),
        'json_key_file_path' => storage_path('keys/glamstack-google-workspace/test.json'),
        'log_channels' => ['single'],
        'subject_email' => config('tests.connections.test.subject_email')
    ]);
    $method_client = new MethodFake($api_client);
    $method_client->setUp();
    expect($method_client->getCustomerId())->toBe(env('GOOGLE_WORKSPACE_TEST_CUSTOMER_ID'));
});

test('setLogChannels() - it can set log channels from a connection key', function(){
    $api_client = new ApiClientFake('test');
    $method_client = new MethodFake($api_client);
    $method_client->setUp();
    expect($method_client->getLogChannels())->toBe(['single']);
});

test('setLogChannels() - it can set log channels from the connection config array', function(){
    $api_client = new ApiClientFake(null, [
        'api_scopes' => [
            'https://www.googleapis.com/auth/admin.directory.group',
            'https://www.googleapis.com/auth/contacts'
        ],
        'customer_id' => config('tests.connections.test.customer_id'),
        'domain' => config('tests.connections.test.domain'),
        'json_key_file_path' => storage_path('keys/glamstack-google-workspace/test.json'),
        'log_channels' => ['single'],
        'subject_email' => config('tests.connections.test.subject_email')
    ]);
    $method_client = new MethodFake($api_client);
    $method_client->setUp();
    expect($method_client->getLogChannels())->toBe(['single']);
});

test('getConfigApiScopes() - it can get the api scopes from configuration file', function(){
    $api_client = new ApiClientFake('test');
    $method_client = new MethodFake($api_client);
    $method_client->setUp();
    $api_scopes = $method_client->getConfigApiScopes('test');
    expect($api_scopes)->toBe(config('tests.connections.test.api_scopes'));
});

test('getConfigApiScopes() - it will throw error if scopes are not set', function(){
    $api_client = new ApiClientFake('test');
    $method_client = new MethodFake($api_client);
    $method_client->setUp();
    $api_scopes = $method_client->getConfigApiScopes('test');
    expect($api_scopes)->toBe(config('tests.connections.test.api_scopes'));
});

test('getConfigSubjectEmail() - it can get the subject email from configuration file', function(){
    $api_client = new ApiClientFake('test');
    $method_client = new MethodFake($api_client);
    $method_client->setUp();
    $subject_email = $method_client->getConfigSubjectEmail('test');
    expect($subject_email)->toBe(config('tests.connections.test.subject_email'));
});

test('getConfigSubjectEmail() - it will set subject_email to null if not provided', function(){
    $api_client = new ApiClientFake('test');
    $method_client = new MethodFake($api_client);
    $method_client->setUp();
    $subject_email = $method_client->getConfigSubjectEmail('test');
    expect($subject_email)->toBe(config('tests.connections.test.subject_email'));
});

test('getConfigJsonFilePath() - it will get the json file path from configuration file', function(){
    $api_client = new ApiClientFake('test');
    $method_client = new MethodFake($api_client);
    $method_client->setUp();
    $json_key_file_path = $method_client->getConfigJsonFilePath('test');
    expect($json_key_file_path)->toBe(config('tests.connections.test.json_key_file_path'));
});

test('getConfigJsonFilePath() - it will throw error if file_path is not set', function(){
    $api_client = new ApiClientFake('test_with_no_json_key_file_path');
    $method_client = new MethodFake($api_client);
})->expectExceptionMessage('The configuration file does not contain a json_key_file_path');

test('getConfigArrayApiScopes() - it will get the api scopes from config array', function(){
    $api_scopes = [
        'https://www.googleapis.com/auth/admin.directory.group',
            'https://www.googleapis.com/auth/contacts'
    ];
    $api_client = new ApiClientFake(null, [
        'api_scopes' => $api_scopes,
        'customer_id' => config('tests.connections.test.customer_id'),
        'domain' => config('tests.connections.test.domain'),
        'json_key_file_path' => storage_path('keys/glamstack-google-workspace/test.json'),
        'log_channels' => ['single'],
        'subject_email' => config('tests.connections.test.subject_email')
    ]);
    $method_client = new MethodFake($api_client);
    $api_scopes = $method_client->getConfigArrayJsonKey($api_client->connection_config);
    expect($api_scopes)->toBe($api_scopes);
});

test('getConfigArraySubjectEmail() - it will get the subject email from config array', function(){
    $api_scopes = [
        'https://www.googleapis.com/auth/admin.directory.group',
        'https://www.googleapis.com/auth/contacts'
    ];
    $api_client = new ApiClientFake(null, [
        'api_scopes' => $api_scopes,
        'customer_id' => config('tests.connections.test.customer_id'),
        'domain' => config('tests.connections.test.domain'),
        'json_key_file_path' => storage_path('keys/glamstack-google-workspace/test.json'),
        'log_channels' => ['single'],
        'subject_email' => config('tests.connections.test.subject_email')
    ]);
    $method_client = new MethodFake($api_client);
    $subject_email = $method_client->getConfigArraySubjectEmail($api_client->connection_config);
    expect($subject_email)->toBe(config('tests.connections.test.subject_email'));
});

test('getConfigArrayFilePath() - it will get the file path from config array', function(){
    $api_scopes = [
        'https://www.googleapis.com/auth/admin.directory.group',
        'https://www.googleapis.com/auth/contacts'
    ];
    $api_client = new ApiClientFake(null, [
        'api_scopes' => $api_scopes,
        'customer_id' => config('tests.connections.test.customer_id'),
        'domain' => config('tests.connections.test.domain'),
        'json_key_file_path' => storage_path('keys/glamstack-google-workspace/test.json'),
        'log_channels' => ['single'],
        'subject_email' => config('tests.connections.test.subject_email')
    ]);
    $method_client = new MethodFake($api_client);
    $file_path = $method_client->getConfigArrayFilePath($api_client->connection_config);
    expect($file_path)->toBe(config('tests.connections.test.json_key_file_path'));
});

test('getConfigArrayJsonKey() - it will get the json_key from config array', function(){
    $api_scopes = [
        'https://www.googleapis.com/auth/admin.directory.group',
        'https://www.googleapis.com/auth/contacts'
    ];
    $json_key = fopen(storage_path('keys/glamstack-google-workspace/test.json'), 'r');
    $file_contents = fread($json_key,filesize(storage_path('keys/glamstack-google-workspace/test.json')));
    fclose($json_key);
    $api_client = new ApiClientFake(null, [
        'api_scopes' => $api_scopes,
        'customer_id' => config('tests.connections.test.customer_id'),
        'domain' => config('tests.connections.test.domain'),
        'json_key' => $file_contents,
        'log_channels' => ['single'],
        'subject_email' => config('tests.connections.test.subject_email')
    ]);
    $method_client = new MethodFake($api_client);
    $key = $method_client->getConfigArrayJsonKey($api_client->connection_config);
    expect($key)->toBe($file_contents);
});

test('appendRequiredHeaders() - it appends required headers', function(){
    $api_client = new ApiClientFake('test');
    $method_client = new MethodFake($api_client);
    $method_client->setUp();
    $headers = $method_client->appendRequiredHeaders([
        'example_name_name' => 'test-header'
    ]);
    expect($headers)->toBe([
        'example_name_name' => 'test-header',
        'domain' => config('tests.connections.test.domain'),
        'customer' => config('tests.connections.test.customer_id')
    ]);
});

test('checkForPagination() - it returns true for paginated response from GET request', function(){
    $api_client = new ApiClientFake('test');
    $method_client = new MethodFake($api_client);
    $method_client->setUp();
    Http::fake([
        '*' => Http::response([
            'type'       => 'example',
            'id'         => 'some id',
            'attributes' => [
                'email'      => 'some email',
                'uuid'       => 'some uuid',
                'created_at' => 'some created_at',
                'updated_at' => 'some updated_at',
            ],
            'nextPageToken' => 'asd'
        ], 200, []),
    ]);
    $test = Http::get('*');
    $paginated = $method_client->checkForPagination($test);
    expect($paginated)->toBeTrue();
});

test('checkForPagination() - it returns false for non-paginated response from GET request', function(){
    $api_client = new ApiClientFake('test');
    $method_client = new MethodFake($api_client);
    $method_client->setUp();
    Http::fake([
        '*' => Http::response([
            'type'       => 'example',
            'id'         => 'some id',
            'attributes' => [
                'email'      => 'some email',
                'uuid'       => 'some uuid',
                'created_at' => 'some created_at',
                'updated_at' => 'some updated_at',
            ],
        ], 200, []),
    ]);
    $test = Http::get('*');
    $paginated = $method_client->checkForPagination($test);
    expect($paginated)->toBeFalse();
});

test('parseConfigFile() - it can parse the configuration file', function(){
    $api_client = new ApiClientFake('test');
    $method_client = new MethodFake($api_client);
    $method_client->setUp();
    $file_contents = $method_client->parseConfigFile('test');
    expect($file_contents)->toBe([
        'api_scopes' => config('tests.connections.test.api_scopes'),
        'subject_email' => config('tests.connections.test.subject_email'),
        'json_key_file_path' => config('tests.connections.test.json_key_file_path')
    ]);
});

test('parseConnectionConfigArray() - it can parse the connection config array appropriately', function(){
    $api_scopes = [
        'https://www.googleapis.com/auth/admin.directory.group',
        'https://www.googleapis.com/auth/contacts'
    ];
    $connnection_array = [
        'api_scopes' => $api_scopes,
        'customer_id' => config('tests.connections.test.customer_id'),
        'domain' => config('tests.connections.test.domain'),
        'json_key_file_path' => storage_path('keys/glamstack-google-workspace/test.json'),
        'log_channels' => ['single'],
        'subject_email' => config('tests.connections.test.subject_email')
    ];

    $expected_result = [
        'api_scopes' => $api_scopes,
        'subject_email' => config('tests.connections.test.subject_email'),
        'json_key_file_path' => storage_path('keys/glamstack-google-workspace/test.json'),
        'json_key' => null
    ];
    $api_client = new ApiClientFake(null, $connnection_array);
    $method_client = new MethodFake($api_client);
    $array_contents = $method_client->parseConnectionConfigArray($connnection_array);
    expect($array_contents)->toBe($expected_result);
});


