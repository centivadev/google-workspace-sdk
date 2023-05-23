<?php

namespace GitlabIt\GoogleWorkspace\Tests\Feature\src\Resources\Vault;

use GitlabIt\GoogleWorkspace\Tests\Fakes\ApiClientFake;

test('get() - it can list vault matters', function(){
    $api_client = new ApiClientFake('test');

    $response = $api_client->vault()->get('/matters' );
    expect($response->status->successful)->toBeTrue();
});
