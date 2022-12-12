<?php

namespace Glamstack\GoogleWorkspace\Resources\Vault;

use Exception;
use Glamstack\GoogleWorkspace\ApiClient;
use Glamstack\GoogleWorkspace\Models\Resources\Vault\VaultModel;

class Vault extends ApiClient
{
    public const BASE_URL = "https://vault.googleapis.com/v1";

    public function __construct(?string $connection_key = null, ?array $connection_config = [])
    {
        parent::__construct($connection_key, $connection_config);

        $vault_model = new VaultModel();

        if(empty($connection_config)){
            $this->setConnectionKey($connection_key);
            $this->connection_config = [];
        } else {
            $this->connection_config = $vault_model->verifyConfigArray($connection_config);
            $this->connection_key = null;
        }
    }

    /**
     * GET HTTP Request
     *
     * This will perform a GET request against the provided `url`. There is no
     * validation for the provided URL or request data in this method. (i.e.
     * `https://admin.googleapis.com/admin/directory/v1/groups`)
     *
     * @param string $url
     *      The Google URL to run the GET request with
     *
     * @param array $request_data
     *      Request data to load into GET request `Request Body`
     *
     * @return object|string
     *
     * @throws Exception
     */
    public function get(string $url, array $request_data = []): object|string
    {
        $method = new Method($this);
        return $method->get(self::BASE_URL . $url, $request_data);
    }

    /**
     * POST HTTP Request
     *
     * This will perform a POST request against the provided `url`. There is no
     * validation for the provided URL or request data in this method. (i.e
     * `https://admin.googleapis.com/admin/directory/v1/groups`)
     *
     * @param string $url
     *      The Google URL to run the POST request with
     *
     * @param array|null $request_data
     *      Request data to load into POST request `Request Body`
     *
     * @return object|string
     *
     * @throws Exception
     */
    public function post(string $url, ?array $request_data = []): object|string
    {
        $method = new Method($this);
        return $method->post(self::BASE_URL . $url, $request_data);
    }

    /**
     * PATCH HTTP Request
     *
     * This will perform a PATCH request against the provided `url`. There is no
     * validation for the provided URL or request data in this method. (i.e
     * `https://admin.googleapis.com/admin/directory/v1/groups`)
     *
     * @param string $url
     *      The Google URL to run the PATCH request with
     *
     * @param array $request_data
     *      Request data to load into PATCH request `Request Body`
     *
     * @return object|string
     *
     * @throws Exception
     */
    public function patch(string $url, array $request_data = []): object|string
    {
        $method = new Method($this);
        return $method->patch(self::BASE_URL . $url, $request_data);
    }

    /**
     * PUT HTTP Request
     *
     * This will perform a PUT request against the provided `url`. There is no
     * validation for the provided URL or request data in this method. (i.e
     * `https://admin.googleapis.com/admin/directory/v1/groups`)
     *
     * @param string $url
     *      The Google URL to run the PUT request with
     *
     * @param array $request_data
     *      Request data to load into PUT request `Request Body`
     *
     * @return object|string
     *
     * @throws Exception
     */
    public function put(string $url, array $request_data = []): object|string
    {
        $method = new Method($this);
        return $method->put(self::BASE_URL . $url, $request_data);
    }

    /**
     * DELETE HTTP Request
     *
     * This will perform a DELETE request against the provided `url`. There is no
     * validation for the provided URL or request data in this method. (i.e
     * `https://admin.googleapis.com/admin/directory/v1/groups`)
     *
     * @param string $url
     *      The Google URL to run the DELETE request with
     *
     * @param array $request_data
     *      Request data to load into DELETE request `Request Body`
     *
     * @return object|string
     *
     * @throws Exception
     */
    public function delete(string $url, array $request_data = []): object|string
    {
        $method = new Method($this);
        return $method->delete(self::BASE_URL . $url, $request_data);
    }
}
