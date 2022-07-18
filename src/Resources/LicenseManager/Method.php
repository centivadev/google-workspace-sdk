<?php

namespace Glamstack\GoogleWorkspace\Resources\LicenseManager;

use Glamstack\GoogleWorkspace\ApiClient;
use Glamstack\GoogleWorkspace\Resources\BaseClient;

class Method extends BaseClient
{

    protected string $customer_id;

    public function __construct(ApiClient $api_client)
    {
        parent::__construct($api_client);
        $this->setCustomerId();
    }

    /**
     * Get the customer_id class level variable
     *
     * @return string
     */
    protected function getCustomerId(): string
    {
        return $this->customer_id;
    }

    /**
     * Set the project_id class level variable
     *
     * @return void
     */
    protected function setCustomerId(): void
    {
        if ($this->api_client->connection_key) {
            $this->customer_id = config(
                $this->api_client->config_path . '.connections.' .
                $this->api_client->connection_key . '.customer_id'
            );
        } else {
            $this->customer_id = $this->api_client->connection_config['customer_id'];
        }
    }

    /**
     * Append required headers to request_data
     *
     * The required headers for Google Workspace are the `domain` and `customer`
     * variables
     *
     * @param array $request_data
     *      The request data being passed into the HTTP request
     *
     * @return array
     */
    protected function appendRequiredHeaders(array $request_data): array
    {
        $required_parameters = [
            'customerId' => $this->customer_id
        ];

        return array_merge($request_data, $required_parameters);
    }

    /**
     * Run generic GET request on Google URL
     *
     * @param string $url
     *      The URL to run the GET request on (i.e `https://admin.googleapis.com/admin/directory/v1/groups/<group_id>`)
     *
     * @param array $request_data
     *      Optional array data to pass into the GET request
     *
     * @return object|string
     */
    public function get(string $url, array $request_data = []): object|string
    {
        $request_data = $this->appendRequiredHeaders($request_data);

        return BaseClient::getRequest($url, $request_data);
    }

    /**
     * Run generic POST request on Google URL
     *
     * @param string $url
     *      The URL to run the POST request on (i.e `https://admin.googleapis.com/admin/directory/v1/groups/<group_id>`)
     *
     * @param array|null $request_data
     *      Optional array data to pass into the POST request
     *
     * @return object|string
     */
    public function post(string $url, ?array $request_data = []): object|string
    {
        $request_data = $this->appendRequiredHeaders($request_data);

        return BaseClient::postRequest($url, $request_data);
    }

    /**
     * Run generic PATCH request on Google URL
     *
     * @param string $url
     *      The URL to run the PATCH request on (i.e `https://admin.googleapis.com/admin/directory/v1/groups/<group_id>`)
     *
     * @param array $request_data
     *      Optional array data to pass into the PATCH request
     *
     * @return object|string
     */
    public function patch(string $url, array $request_data = []): object|string
    {
        $request_data = $this->appendRequiredHeaders($request_data);

        return BaseClient::patchRequest($url, $request_data);
    }

    /**
     * Run generic PUT request on Google URL
     *
     * @param string $url
     *      The URL to run the PUT request on (i.e `https://admin.googleapis.com/admin/directory/v1/groups/<group_id>`)
     *
     * @param array $request_data
     *      Optional array data to pass into the PUT request
     *
     * @return object|string
     */
    public function put(string $url, array $request_data = []): object|string
    {
        $request_data = $this->appendRequiredHeaders($request_data);

        return BaseClient::putRequest($url, $request_data);
    }

    /**
     * Run generic DELETE request on Google URL
     *
     * @param string $url
     *      The URL to run the DELETE request on (i.e `https://admin.googleapis.com/admin/directory/v1/groups/<group_id>`)
     *
     * @param array $request_data
     *      Optional array data to pass into the DELETE request
     *
     * @return object|string
     */
    public function delete(string $url, array $request_data = []): object|string
    {
        $request_data = $this->appendRequiredHeaders($request_data);

        return BaseClient::deleteRequest($url, $request_data);
    }
}
