<?php

namespace Glamstack\GoogleWorkspace\Resources\Directory;

use Exception;
use Glamstack\GoogleWorkspace\ApiClient;
use Glamstack\GoogleWorkspace\Models\Resources\Directory\DirectoryModel;

class Directory extends ApiClient
{
    public const BASE_URL = "https://admin.googleapis.com/admin/directory/v1";

    public function __construct(?string $connection_key = null, ?array $connection_config = [])
    {
        parent::__construct($connection_key, $connection_config);

        $directory_model = new DirectoryModel();

        if(empty($connection_config)){
            $this->setConnectionKey($connection_key);
            $this->connection_config = [];
        } else {
            $this->connection_config = $directory_model->verifyConfigArray($connection_config);
            $this->connection_key = null;
        }
    }

    /**
     * GET HTTP Request
     *
     * This will perform a GET request against the provided `uri`. There is no
     * validation for the provided URI or request data in this method. (i.e.
     * `https://admin.googleapis.com/admin/directory/v1/groups`)
     *
     * @param string $uri
     *      The Google URI to run the GET request with after `/v1`
     *
     * @param array $request_data
     *      Request data to load into GET request `Request Body`
     *
     * @param bool $exclude_domain
     *      Remove the `domain` parameter from the GET request header
     *
     * @param bool $exclude_customer
     *      Remove the `customer` parameter from the GET request header
     *
     * @return object|string
     *
     * @throws Exception
     */
    public function get(string $uri, array $request_data = [], bool $exclude_domain = false, bool $exclude_customer = false): object|string
    {
        $method = new Method($this);
        return $method->get(self::BASE_URL . $uri, $request_data, $exclude_domain, $exclude_customer);
    }

    /**
     * POST HTTP Request
     *
     * This will perform a POST request against the provided `uri`. There is no
     * validation for the provided URI or request data in this method. (i.e
     * `https://admin.googleapis.com/admin/directory/v1/groups`)
     *
     * @param string $uri
     *      The Google URI to run the POST request with after `/v1`
     *
     * @param array|null $request_data
     *      Request data to load into POST request `Request Body`
     *
     * @return object|string
     *
     * @throws Exception
     */
    public function post(string $uri, ?array $request_data = []): object|string
    {
        $method = new Method($this);
        return $method->post(self::BASE_URL . $uri, $request_data);
    }

    /**
     * PATCH HTTP Request
     *
     * This will perform a PATCH request against the provided `uri`. There is no
     * validation for the provided URI or request data in this method. (i.e
     * `https://admin.googleapis.com/admin/directory/v1/groups`)
     *
     * @param string $uri
     *      The Google URI to run the PATCH request with after `/v1`
     *
     * @param array $request_data
     *      Request data to load into PATCH request `Request Body`
     *
     * @return object|string
     *
     * @throws Exception
     */
    public function patch(string $uri, array $request_data = []): object|string
    {
        $method = new Method($this);
        return $method->patch(self::BASE_URL . $uri, $request_data);
    }

    /**
     * PUT HTTP Request
     *
     * This will perform a PUT request against the provided `uri`. There is no
     * validation for the provided URI or request data in this method. (i.e
     * `https://admin.googleapis.com/admin/directory/v1/groups`)
     *
     * @param string $uri
     *      The Google URI to run the PUT request with after `/v1`
     *
     * @param array $request_data
     *      Request data to load into PUT request `Request Body`
     *
     * @return object|string
     *
     * @throws Exception
     */
    public function put(string $uri, array $request_data = []): object|string
    {
        $method = new Method($this);
        return $method->put(self::BASE_URL . $uri, $request_data);
    }

    /**
     * DELETE HTTP Request
     *
     * This will perform a DELETE request against the provided `uri`. There is no
     * validation for the provided URI or request data in this method. (i.e
     * `https://admin.googleapis.com/admin/directory/v1/groups`)
     *
     * @param string $uri
     *      The Google URI to run the DELETE request with after `/v1`
     *
     * @param array $request_data
     *      Request data to load into DELETE request `Request Body`
     *
     * @return object|string
     *
     * @throws Exception
     */
    public function delete(string $uri, array $request_data = []): object|string
    {
        $method = new Method($this);
        return $method->delete(self::BASE_URL . $uri, $request_data);
    }
}
