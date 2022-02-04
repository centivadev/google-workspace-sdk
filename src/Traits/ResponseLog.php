<?php

namespace Glamstack\GoogleAuth\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait ResponseLog{

    /**
     * Create a log entry for an API call
     *
     * This method is called from other methods and will call specific methods
     * depending on the log severity level.
     *
     * @param string $method
     *      The lowercase name of the method that calls this function (ex. `get`)
     *
     * @param string $url
     *      The URL of the API call including the concatenated base URL and URI
     *
     * @param object $response
     *      The HTTP response formatted with $this->parseApiResponse()
     *
     * @return void
     */
    public function logResponse(string $method, string $url, object $response) : void
    {
        // Status code log messages (2xx, 4xx, 5xx)
        if ($response->status->ok == true) {
            $this->logInfo($method, $url, $response);
        } elseif ($response->status->clientError == true) {
            $this->logClientError($method, $url, $response);
        } elseif ($response->status->serverError == true) {
            $this->logServerError($method, $url, $response);
        }
    }
}
