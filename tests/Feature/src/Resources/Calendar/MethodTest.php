<?php

namespace Glamstack\GoogleWorkspace\Tests\Feature\src\Resources\Calendar;

use Glamstack\GoogleWorkspace\Tests\Fakes\ApiClientFake;

test('get() - it can use GET to list calendars', function() {
    $api_client = new ApiClientFake('test');
    $response = $api_client->calendar()->get('/users/me/calendarList');
    expect($response->status->code)->toBe(200)
        ->and(property_exists($response->object, 'items'))->toBeTrue();
});

test('post() - it can use POST to create calendar event', function(){
    $api_client = new ApiClientFake('test');
    $response = $api_client->calendar()->post(
        '/calendars/' . config('tests.connections.test.subject_email') . '/events',
        [
            'start' => [
                'date' => '2023-02-22',
            ],
            'end' => [
                'date' => '2023-02-22',
            ]
        ]
    );
    expect($response->status->successful)->toBeTrue();
});


