<?php

namespace GitlabIt\GoogleWorkspace\Tests\Feature\src\Resources\LicenseManager;

use GitlabIt\GoogleWorkspace\Tests\Fakes\ApiClientFake;

test('get() - it can list all users for google-apps', function () {
    $api_client = new ApiClientFake('test');
    $response = $api_client->licenseManager()->get('/Google-Apps/users');
    expect($response->status->successful)->toBeTrue();
});
