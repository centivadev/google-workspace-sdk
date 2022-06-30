<?php

namespace Glamstack\GoogleWorkspace\Resources\Rest;

use Glamstack\GoogleWorkspace\Resources\BaseClient;

class Method extends BaseClient
{
    /**
     * Run generic GET request on Google URI
     *
     * @param string $uri
     *      The URI to run the GET request on
     *
     * @param array $request_data
     *      Optional array data to pass into the GET request
     *
     * @return object|string
     */
    public function get(string $uri, array $request_data = []): object|string
    {
        return BaseClient::getRequest($uri, $request_data);
    }

    /**
     * Run generic POST request on Google URI
     *
     * @param string $uri
     *      The URI to run the POST request on
     *
     * @param array|null $request_data
     *      Optional array data to pass into the POST request
     *
     * @return object|string
     */
    public function post(string $uri, ?array $request_data = []): object|string
    {
        return BaseClient::postRequest($uri, $request_data);
    }

    /**
     * Run generic PATCH request on Google URI
     *
     * @param string $uri
     *      The URI to run the PATCH request on
     *
     * @param array $request_data
     *      Optional array data to pass into the PATCH request
     *
     * @return object|string
     */
    public function patch(string $uri, array $request_data = []): object|string
    {
        return BaseClient::patchRequest($uri, $request_data);
    }

    /**
     * Run generic PUT request on Google URI
     *
     * @param string $uri
     *      The URI to run the PUT request on
     *
     * @param array $request_data
     *      Optional array data to pass into the PUT request
     *
     * @return object|string
     */
    public function put(string $uri, array $request_data = []): object|string
    {
        return BaseClient::putRequest($uri, $request_data);
    }

    /**
     * Run generic DELETE request on Google URI
     *
     * @param string $uri
     *      The URI to run the DELETE request on
     *
     * @param array $request_data
     *      Optional array data to pass into the DELETE request
     *
     * @return object|string
     */
    public function delete(string $uri, array $request_data = []): object|string
    {
        return BaseClient::deleteRequest($uri, $request_data);
    }
}
