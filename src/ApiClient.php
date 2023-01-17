<?php

namespace Glamstack\GoogleWorkspace;

use Exception;
use Glamstack\GoogleAuth\AuthClient;
use Glamstack\GoogleWorkspace\Models\ApiClientModel;
use Glamstack\GoogleWorkspace\Resources\Calendar\Calendar;
use Glamstack\GoogleWorkspace\Resources\Directory\Directory;
use Glamstack\GoogleWorkspace\Resources\Drive\Drive;
use Glamstack\GoogleWorkspace\Resources\Gmail\Gmail;
use Glamstack\GoogleWorkspace\Resources\LicenseManager\LicenseManager;
use Glamstack\GoogleWorkspace\Resources\Sheets\Sheets;
use Glamstack\GoogleWorkspace\Traits\ResponseLog;

class ApiClient
{
    use ResponseLog;

    public array $connection_config;
    public ?string $connection_key;
    public array $request_headers;
    public string $config_path;
    protected string $auth_token;
    public array $log_channels;

    /**
     * This function takes care of the initialization of authentication using
     * the `Glamstack\GoogleAuth\AuthClient` class to connect to Google OAuth2
     * servers to retrieve an API token to be used with Google API endpoints.
     *
     * @see https://gitlab.com/gitlab-com/business-technology/engineering/access-manager/packages/composer/gitlab-sdk/-/blob/main/README.md
     *
     * @see https://developers.google.com/admin-sdk/directory/reference/rest/v1/users/list#:~:text=must%20be%20provided.-,domain,-string
     *
     * @see https://support.google.com/a/answer/162106
     *
     * @param ?string $connection_key
     *      (Optional) The connection key to use from the configuration file to
     *      set the appropriate Google Auth and Google Workspace settings.
     *
     *      Default: `workspace`
     * @throws \Exception
     */
    function __construct(
        ?string $connection_key = null,
        ?array $connection_config = []
    ) {
        $api_client_model = new ApiClientModel();

        $this->setConfigPath();

        $this->setRequestHeaders();

        if(empty($connection_config)){
            $this->setConnectionKey($connection_key);
            $this->connection_config = [];
        } else {
            $this->connection_config = $api_client_model->verifyConfigArray($connection_config);
            $this->connection_key = null;
        }

        // Set the request headers to be used by the API client
        $this->setRequestHeaders();
    /**
     * Parse the configuration file to get config parameters
     *
     * @param string $connection_key
     *      The connection key provided during initialization of the SDK
     *
     * @return array
     * @throws Exception
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
     * @throws Exception
     */
    protected function getConfigApiScopes(string $connection_key): array
    {
        $api_scope_path = $this->config_path . '.connections.' . $connection_key . '.api_scopes';
        if (config($api_scope_path)) {

            $this->logInfo('Success - Getting configuration file api_scopes value', [
                'api_scopes' => config($api_scope_path)
            ]);

            return config($this->config_path . '.connections.' . $connection_key . '.api_scopes');
        } else {
            throw new Exception('No api_scopes have been set in the configuration file you are using.');
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
        $config_path = $this->config_path . '.connections.' . $connection_key;
        if (array_key_exists('subject_email', config($config_path))) {
            if (config($config_path . '.subject_email')) {
                $this->logInfo('Success - Getting configuration file subject_email value', [
                    'subject_email' => config($config_path . '.subject_email')
                ]);
                return config($config_path . '.subject_email');
            } else {
                $this->logInfo('Success - Setting subject_email value to null');
                return null;
            }
        } else {
            $this->logInfo('Success - Setting subject_email value to null');
            return null;
        }
    }

    /**
     * Set the config path
     */
    public function setConfigPath(){
        $this->config_path = env('GLAMSTACK_GOOGLE_WORKSPACE_CONFIG_PATH', 'glamstack-google-workspace');
    }

    /**
     * @throws \Exception
     */
    public function drive(): Drive
    {
        return new Drive($this->connection_key, $this->connection_config);
    }

    /**
     * @throws \Exception
     */
    public function directory(): Directory
    {
        return new Directory($this->connection_key, $this->connection_config);
    }

    public function gmail(): Gmail
    {
        return new Gmail($this->connection_key, $this->connection_config);
    }


    public function sheets(): Sheets
    {
        return new Sheets($this->connection_key, $this->connection_config);
    }

    public function licenseManager(): LicenseManager
    {
        return new LicenseManager($this->connection_key, $this->connection_config);
    }

    public function calendar(): Calendar
    {
        return new Calendar($this->connection_key, $this->connection_config);
    }

    /**
     * Set the connection_key class variable. The connection_key variable by default
     * will be set to `workspace`. This can be overridden when initializing the
     * SDK with a different connection key which is passed into this function to
     * set the class variable to the provided key.
     *
     * @param ?string $connection_key (Optional) The connection key to use from the
     * configuration file.
     *
     * @return void
     */
    protected function setConnectionKey(?string $connection_key): void
    {
        if($connection_key == null) {
            $this->connection_key = config(
                $this->config_path . '.default.connection'
            );
        } else {
            $this->connection_key = $connection_key;
        }
    }

    /**
     * Set the request headers for the Google Cloud API request
     *
     * @return void
     */
    protected function setRequestHeaders(): void
    {
        // Get Laravel and PHP Version
        $laravel = 'laravel/'.app()->version();
        $php = 'php/'.phpversion();

        // Decode the composer.lock file
        $composer_lock_json = json_decode(
            (string) file_get_contents(base_path('composer.lock')),
            true
        );

        // Use Laravel collection to search for the package. We will use the
        // array to get the package name (in case it changes with a fork) and
        // return the version key. For production, this will show a release
        // number. In development, this will show the branch name.
        /** @phpstan-ignore-next-line */
        $composer_package = collect($composer_lock_json['packages'])
            ->where('name', 'glamstack/google-workspace-sdk')
            ->first();

        /** @phpstan-ignore-next-line */
        if($composer_package){
            $package = $composer_package['name'].'/'.$composer_package['version'];
        } else {
            $package = 'dev-google-workspace-sdk';
        }

        // Define request headers
        $this->request_headers = [
            'User-Agent' => $package.' '.$laravel.' '.$php
        ];
    }
}


