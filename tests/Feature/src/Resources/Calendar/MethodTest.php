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

test('put() - it can update a calendar event', function(){
    $api_client = new ApiClientFake('test');
    $get_response = $api_client->calendar()->get(
        '/calendars/' . config('tests.connections.test.subject_email') . '/events'
    );
    $first_event = collect($get_response->object->items)->first();
    $response = $api_client->calendar()->put(
        '/calendars/' . config('tests.connections.test.subject_email') . '/events/' . $first_event->id,
        [
            'start' => [
                'date' => '2023-02-22',
            ],
            'end' => [
                'date' => '2023-02-22',
            ]
        ]
    );
    expect($response->object->start->date)->toBe('2023-02-22')
        ->and($response->object->end->date)->toBe('2023-02-22');
});

test('delete() - it can delete a calendar event', function(){
    $api_client = new ApiClientFake('test');
    $create_response = $api_client->calendar()->post(
        '/calendars/' . config('tests.connections.test.subject_email') . '/events',
        [
            'start' => [
                'date' => '2023-02-23',
            ],
            'end' => [
                'date' => '2023-02-23',
            ]
        ]
    );
    $new_event_id = $create_response->object->id;
    $delete_response = $api_client->calendar()->delete(
        '/calendars/' . config('tests.connections.test.subject_email') . '/events/' . $new_event_id,
    );
    expect($delete_response->status->code)->toBe(204)
        ->and($delete_response->object)->toBeNull()
        ->and($delete_response->status->successful)->toBeTrue();
});
