<?php

namespace Glamstack\GoogleWorkspace\Tests\Feature\src\Resources\Calendar;

use Glamstack\GoogleWorkspace\Tests\Fakes\ApiClientFake;

test('get() - it can use GET to list calendars', function() {
    $api_client = new ApiClientFake('test');
    $response = $api_client->calendar()->get('/users/me/calendarList');
    expect($response->status->code)->toBe(200)
        ->and(property_exists($response->object, 'items'))->toBeTrue();
});
