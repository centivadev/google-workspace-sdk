<?php

namespace Glamstack\GoogleWorkspace\Resources;

use Exception;
use Glamstack\GoogleAuth\AuthClient;
use Glamstack\GoogleWorkspace\ApiClient;
use Glamstack\GoogleWorkspace\Traits\ResponseLog;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

abstract class BaseClient
{
    use ResponseLog;

    protected ApiClient $api_client;
    protected string $customer_id;
    protected string $domain;
    protected array $log_channels;
    private string $auth_token;

    /**
     * @throws Exception
     */
    public function __construct(
        ApiClient $api_client
    )
    {
        // Initialize Google Auth SDK
        $this->api_client = $api_client;

        $this->setCustomerId();

        $this->setDomain();

        $this->setLogChannels();

        if ($this->api_client->connection_key) {
            $google_auth = new AuthClient(
                $this->parseConfigFile($this->api_client->connection_key)
            );
        } else {
            $google_auth = new AuthClient(
                $this->parseConnectionConfigArray($this->api_client->connection_config)
            );
        }

        // Authenticate with Google OAuth2 Server auth_token
        try {
            $this->auth_token = $google_auth->authenticate();
        } catch (Exception $exception) {
//            $this->logLocalError($exception);
            throw $exception;
        }
    }

    /**
     * Parse the configuration file to get config parameters
     *
     * @param string $connection_key
     *      The connection key provided during initialization of the SDK
     *
     * @return array
     */
    protected function parseConfigFile(string $connection_key): array
    {
        return [
            'api_scopes' => $this->getConfigApiScopes($connection_key),
            'subject_email' => $this->getConfigSubjectEmail($connection_key),
            'json_key_file_path' => $this->getConfigJsonFilePath($connection_key),
        ];
    }

    /**
     * Get the api_scopes from the configuration file
     *
     * @param string $connection_key
     *     The connection key provided during initialization of the SDK
     *
     * @return array
     */
    protected function getConfigApiScopes(string $connection_key): array
    {
        if (config($this->api_client->config_path . '.connections.' . $connection_key . '.api_scopes')) {
            return config($this->api_client->config_path . '.connections.' .
                $connection_key . '.api_scopes');
        } else {
            //TODO: return error
            dd('no api_scopes set error');
        }
    }
}
