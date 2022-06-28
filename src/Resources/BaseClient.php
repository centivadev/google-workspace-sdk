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

    /**
     * Get the subject_email from the configuration file
     *
     * Subject email is not required so if not set then return null
     *
     * @param string $connection_key
     *      The connection key provided during initialization of the SDK
     *
     * @return string|null
     */
    protected function getConfigSubjectEmail(string $connection_key): string|null
    {
        $config_path = $this->api_client->config_path . '.connections.' . $connection_key;
        if (array_key_exists('subject_email', config($config_path))) {
            return config($config_path . '.subject_email');
        } else {
            return null;
        }
    }

    /**
     * Get the json_key_file from the configuration file
     *
     * This is required if using the configuration file
     *
     * @param string $connection_key
     *      The connection key provided during initialization of the SDK
     *
     * @return string|null
     * @throws Exception
     */
    protected function getConfigJsonFilePath(string $connection_key): string|null
    {
        $config_path = $this->api_client->config_path . '.connections.' . $connection_key;
        if (array_key_exists('json_key_file_path', config($config_path))) {
            if (config($this->api_client->config_path . '.connections.' . $connection_key .
                '.json_key_file_path')) {
                return config($this->api_client->config_path . '.connections.' . $connection_key .
                    '.json_key_file_path');
            } else {
                throw new Exception('The configuration file does not contain a json_key_file_path');
            }
        } else {
            throw new Exception('The configuration file does not contain a json_key_file_path');
        }
    }

    /**
     * Parse the connection_config array to get the configuration parameters
     *
     * @param array $connection_config
     *      The connection config array provided during initialization of the SDK
     *
     * @return array
     */
    protected function parseConnectionConfigArray(array $connection_config): array
    {
        return [
            'api_scopes' => $this->getConfigArrayApiScopes($connection_config),
            'subject_email' => $this->getConfigArraySubjectEmail($connection_config),
            'json_key_file_path' => $this->getConfigArrayFilePath($connection_config),
            'json_key' => $this->getConfigArrayJsonKey($connection_config)
        ];
    }

    /**
     * Get the api_scopes from the connection_config array
     *
     * @param array $connection_config
     *      The connection config array provided during initialization of the SDK
     *
     * @return array
     */
    protected function getConfigArrayApiScopes(array $connection_config): array
    {
        return $connection_config['api_scopes'];
    }

    /**
     * Get the subject_email from the connection_config array
     *
     * Subject Email is not required so if not set return null
     *
     * @param array $connection_config
     *      The connection config array provided during initialization of the SDK
     *
     * @return string|null
     */
    protected function getConfigArraySubjectEmail(array $connection_config): string|null
    {
        if (array_key_exists('subject_email', $connection_config)) {
            return $connection_config['subject_email'];
        } else {
            return null;
        }
    }

    /**
     * Get the file_path from the connection_config array
     *
     * file_path is not required to be set so if not set return null
     *
     * @param array $connection_config
     *      The connection config array provided during initialization of the SDK
     *
     * @return string|null
     */
    protected function getConfigArrayFilePath(array $connection_config): string|null
    {
        if (array_key_exists('json_key_file_path', $connection_config)) {
            return $connection_config['json_key_file_path'];
        } else {
            return null;
        }
    }

    /**
     * Get the json_key from the connection_config array
     *
     * json_key i9s not required to be set so if not set return null
     *
     * @param array $connection_config
     *      The connection config array provided during initialization of the SDK
     *
     * @return mixed|null
     */
    protected function getConfigArrayJsonKey(array $connection_config): mixed
    {
        if (array_key_exists('json_key', $connection_config)) {
            return $connection_config['json_key'];
        } else {
            return null;
        }
    }

    /**
     * Check if pagination is used in the Google Cloud GET response.
     *
     * @param Response $response API response from Google Cloud GET request
     *
     * @return bool True if pagination is required | False if not
     * @see GOOGLE PAGINATION EXAMPLE
     *
     */
    protected function checkForPagination(Response $response): bool
    {
        // Check if Google Cloud GET Request object contains `nextPageToken`
        if (property_exists($response->object(), 'nextPageToken')) {
            return true;
        } else {
            return false;
        }
    }
    protected function getLogChannels(): array
    {
        return $this->log_channels;
    }

    /**
     * Set the log_channels class variable
     *
     * @return void
     */
    protected function setLogChannels(): void
    {
        if ($this->api_client->connection_key) {
            $this->log_channels = config(
                $this->api_client->config_path . '.connections.' .
                $this->api_client->connection_key . '.log_channels'
            );
        } else {
            $this->log_channels = $this->api_client->connection_config['log_channels'];
        }
    }

    protected function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * Set the project_id class level variable
     *
     * @return void
     */
    protected function setDomain(): void
    {
        if ($this->api_client->connection_key) {
            $this->domain = config(
                $this->api_client->config_path . '.connections.' .
                $this->api_client->connection_key . '.domain'
            );
        } else {
            $this->domain = $this->api_client->connection_config['domain'];
        }
    }

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
}
