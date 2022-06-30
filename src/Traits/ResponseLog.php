<?php

namespace Glamstack\GoogleWorkspace\Traits;

use Exception;
use http\Client\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait ResponseLog
{

    /**
     * Create a log entry for an API call
     *
     * This method is called from other methods and will call specific methods
     * depending on the log severity level.
     *
     * @param string $url The URL of the API call including the concatenated base URL and URI
     *
     * @param object $response The HTTP response formatted with $this->parseApiResponse()
     *
     * @return void
     */
    public function logResponse(string $url, object $response): void
    {
        $method = debug_backtrace()[1]['function'];

        // Status code log messages (2xx, 4xx, 5xx)
        if ($response->status->ok == true) {
            $this->logResponseInfo($method, $url, $response);
        } elseif ($response->status->clientError == true) {
            $this->logResponseClientError($method, $url, $response);
        } elseif ($response->status->serverError == true) {
            $this->logResponseServerError($method, $url, $response);
        }
    }

    /**
     * Log non-converted HTTP Responses
     *
     * @param string $url
     *      The URL of the HTTP request
     * @param object $response
     *      The Response from the HTTP request
     *
     * @return void
     */
    public function logHttpInfo(string $url, object $response): void
    {
        $method = debug_backtrace()[1]['function'];

        $message = $method.' '.$response->status() .' '.$url;

        Log::stack($this->log_channels)
            ->info($message, [
                'api_endpoint' => $url,
                'api_method' => $method,
                'class' => get_class(),
                'event_type' => 'google-workspace-http-response',
                'message' => $message,
                'response_object' => $response->object() ? $response->object() : null,
                'status_code' => $response->status(),
            ]);
    }

    /**
     * Create an info log entry for an API call
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
    public function logInfo(string $method, string $url, object $response): void
    {
        $message = Str::upper($method) . ' ' . $response->status->code . ' ' . $url;

        Log::stack((array) $this->connection_config['log_channels'])
            ->info($message, [
                'api_endpoint' => $url,
                'api_method' => Str::upper($method),
                'class' => get_class(),
                'connection_key' => $this->connection_key,
                'event_type' => 'google-workspace-api-response-info',
                'message' => $message,
                'status_code' => $response->status->code,
            ]);
    }

    /**
     * Create a notice log entry for an API call for client errors (4xx status)
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
    public function logClientError(string $method, string $url, object $response): void
    {
        $message = Str::upper($method) . ' ' . $response->status->code . ' ' . $url;

        Log::stack((array) $this->connection_config['log_channels'])
            ->notice($message, [
                'api_endpoint' => $url,
                'api_method' => Str::upper($method),
                'class' => get_class(),
                'connection_key' => $this->connection_key,
                'event_type' => 'google-workspace-api-response-client-error',
                'google_error_type' => $response->object->error ?? null,
                'google_error_description' =>  $response->object->error_description ?? null,
                'message' => $message,
                'status_code' => $response->status->code,
            ]);
    }

    /**
     * Create an error log entry for an API call for server errors (5xx status)
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
    public function logServerError(string $method, string $url, object $response): void
    {
        $message = Str::upper($method) . ' ' . $response->status->code . ' ' . $url;

        Log::stack((array) $this->connection_config['log_channels'])
            ->error($message, [
                'api_endpoint' => $url,
                'api_method' => Str::upper($method),
                'class' => get_class(),
                'connection_key' => $this->connection_key,
                'event_type' => 'google-workspace-api-response-server-error',
                'google_error_type' => $response->object->error ?? null,
                'google_error_description' =>  $response->object->error_description ?? null,
                'message' => $message,
                'status_code' => $response->status->code,
            ]);
    }

    /**
     * Create an error log entry when an configuration parameter is missing.
     *
     * @return void
     */
    public function logMissingConfigError(): void
    {
        Log::stack((array) $this->connection_config['log_channels'])
            ->critical($this->error_message, [
                'event_type' => $this->error_event_type,
                'class' => get_class(),
                'status_code' => '501',
                'message' => $this->error_message,
                'connection_key' => $this->connection_key,
            ]);
    }
}
