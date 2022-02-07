<?php

namespace Glamstack\GoogleWorkspace;

use Glamstack\GoogleAuth\AuthClient;
use Glamstack\GoogleWorkspace\Traits\ResponseLog;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class WorkspaceApiClient
{
    use ResponseLog;

    // Standard parameters for building the ApiClient
    const BASE_URL = 'https://admin.googleapis.com/admin/directory/v1';
    const CONFIG_FILE_NAME = 'glamstack-google';

    private string $auth_token;
    private array $connection_config;
    private string $connection_key;
    private string $customer_id;
    private string $domain;
    private string $error_message;
    private array $request_headers;
    private array $required_parameters;

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
     */
    function __construct(
        string $connection_key = null,
    ) {
        // Set the connection key used for getting the correct configuration
        $this->setConnectionKey($connection_key);

        // Define the connection configuration array as a class variable
        $this->setConnectionConfig();

        // Generate the Google OAuth token using the `google-auth-sdk`
        $this->generateAuthToken();

        // Set the request headers to be used by the API client
        $this->setRequestHeaders();

        // Set the Google Domain using the connection configuration
        $this->setDomain();

        // Set the Google Customer ID using the connection configuration
        $this->setCustomerId();

        // Set the required parameters for Google Workspace API calls
        $this->setRequiredParameters();
    }

    /**
     * Set the `connection_key` class variable.
     *
     * The `connection_key` variable will be set to `workspace` by default. This
     * can be overridden in the construct when initializing the SDK which is
     * then passed to this method.
     *
     * @param ?string $connection_key
     *      (Optional) The connection key to use from the configuration file.
     *
     * @return void
     */
    protected function setConnectionKey(?string $connection_key): void
    {
        if ($connection_key == null) {
            $this->connection_key = config(self::CONFIG_FILE_NAME . '.auth.default_connection');
        } else {
            $this->connection_key = $connection_key;
        }
    }

    /**
     * Set the `connection_config` class property array
     *
     * Define an array in the class using the connection configuration in the
     * glamstack-google.php connections array. If connection key does not exist
     * in the conections array, an error log will be created and a 501 abort
     * error will be thrown.
     *
     * @return void
     */
    protected function setConnectionConfig(): void
    {
        if (array_key_exists($this->connection_key, config(self::CONFIG_FILE_NAME . '.connections'))) {
            $this->connection_config = config(self::CONFIG_FILE_NAME . '.connections.' . $this->connection_key);
        } else {
            $error_message = 'The Google connection key is not defined in the ' .
                '`config/' . self::CONFIG_FILE_NAME . '` connections array. ' .
                ' Without this array config, there is no API configuration to ' .
                'connect with.';

            Log::stack((array) $this->connection_config['log_channels'])
                ->critical($error_message, [
                    'event_type' => 'google-api-config-missing-error',
                    'class' => get_class(),
                    'status_code' => '501',
                    'message' => $error_message,
                    'connection_key' => $this->connection_key,
                ]);

            abort(501, $error_message);
        }
    }

    /**
     * Utilize the `GoogleAuth` SDK to generate Google OAuth API token.
     *
     * @see https://gitlab.com/glamstack/google-auth-sdk
     *
     * @return void
     */
    protected function generateAuthToken()
    {
        // Initialize the Google Auth Client
        /** @phpstan-ignore-next-line */
        $google_auth = new \Glamstack\GoogleAuth\AuthClient(
            $this->connection_key
        );

        // Authenticate with Google OAuth2 Server auth_token
        /** @phpstan-ignore-next-line */
        $this->auth_token = $google_auth->authenticate();
    }
    /**
     * Set the request headers for the GitLab API request
     *
     * @return void
     */
    protected function setRequestHeaders(): void
    {
        // Get Laravel and PHP Version
        $laravel = 'laravel/' . app()->version();
        $php = 'php/' . phpversion();

        // Decode the composer.lock file
        $composer_lock_json = json_decode(
            (string) file_get_contents(base_path('composer.lock')),
            true
        );

        // Use Laravel collection to search for the package. We will use the
        // array to get the package name (in case it changes with a fork) and
        // return the version key. For production, this will show a release
        // number. In development, this will show the branch name.
        $composer_package = collect($composer_lock_json['packages'])
            ->where('name', 'glamstack/google-workspace-sdk')
            ->first();

        $package = $composer_package['name'] . '/' . $composer_package['version'];

        // Define request headers
        $this->request_headers = [
            'User-Agent' => $package . ' ' . $laravel . ' ' . $php
        ];
    }

    /**
     * Set the `domain` class variable
     * 
     * The domain class variable will be set to the `domain` element of the 
     * connection key in the config file. If the the value is not set (null), 
     * an error will be logged and a 501 abort will be returned.
     *
     * @return void
     */
    protected function setDomain(): void
    {
        if ($this->connection_config['domain']) {
            $this->domain = $this->connection_config['domain'];
        } else {
            $this->error_message = 'The Google Domain has not been defined ' .
                'in config/' . self::CONFIG_FILE_NAME . ' or provided during the ' .
                'initialization of the WorkspaceApiClient class. Without the ' .
                'domain, Google Workspace API calls cannot be requested.';

            Log::stack((array) $this->connection_config['log_channels'])
                ->critical($this->error_message, [
                    'event_type' => 'google-workspace-domain-config-missing-error',
                    'class' => get_class(),
                    'status_code' => '501',
                    'message' => $this->error_message,
                    'connection_key' => $this->connection_key,
                ]);
            abort(501, $this->error_message);
        }
    }

    /**
     * Set the `customer_id` class variable
     *
     * The customer_id variable will be set to the `customer_id` element of the
     * connection key in the config file. If the the value is not set (null),
     * an error will be logged and a 501 abort will be returned.
     *
     * @return void
     */
    protected function setCustomerId(): void
    {
        if ($this->connection_config['customer_id']) {
            $this->customer_id = $this->connection_config['customer_id'];
        } else {
            $this->error_message = 'The Google Customer ID has not been defined ' .
                'in config/'  . self::CONFIG_FILE_NAME . ' or provided during the ' .
                'initialization of the WorkspaceApiClient class. Without the ' .
                'Customer Id, Google Workspace API calls cannot be requested.';
            $this->error_event_type = 'google-workspace-customer-id-config-missing-error';

            $this->googleMissingConfigError();

            abort(501, $this->error_message);
        }
    }

    /**
     * Set the `required_parameters` class variable
     * 
     * The array will consist of the `domain` and `customer` parameters that are
     * merged with any request query parameters for all Workspace API calls.
     *
     * @return void
     */
    protected function setRequiredParameters(): void
    {
        $this->required_parameters = [
            'domain' => $this->domain,
            'customer' => $this->customer_id
        ];
    }

    /**
     * Google API GET Request
     *
     * Example Usage:
     * ```php
     * $google_workspace_api = new \Glamstack\GoogleWorkspace\ApiClient();
     * $user_key = 'klibby@example.com`;
     * $google_workspace_api->get('/users/' . $user_key);
     * ```
     *
     * Example Response:
     * ```php
     * {
     *   +"headers": {
     *     +"ETag": (truncated)
     *     +"Content-Type": "application/json; charset=UTF-8"
     *     +"Vary": "Origin X-Origin Referer"
     *     +"Date": "Mon, 24 Jan 2022 17:25:15 GMT"
     *     +"Server": "ESF"
     *     +"Content-Length": "1259"
     *     +"X-XSS-Protection": "0"
     *     +"X-Frame-Options": "SAMEORIGIN"
     *     +"X-Content-Type-Options": "nosniff"
     *     +"Alt-Svc": (truncated)
     *   }
     *   +"json": (truncated) // FIXME
     *   +"object": {
     *     +"kind": "admin#directory#user"
     *     +"id": "114522752583947996869"
     *     +"etag": (truncated)
     *     +"primaryEmail": "klibby@example.com"
     *     +"name": {#1248
     *       +"givenName": "Kate"
     *       +"familyName": "Libby"
     *       +"fullName": "Kate Libby"
     *     }
     *     +"isAdmin": true
     *     (truncated)
     *   }
     *   +"status": {
     *     +"code": 200
     *     +"ok": true
     *     +"successful": true
     *     +"failed": false
     *     +"serverError": false
     *     +"clientError": false
     *   }
     * }
     * ```
     *
     * @see https://developers.google.com/admin-sdk/directory/reference/rest
     *
     * @param string $uri
     *      The URI of the Google Workspace API request with a leading slash
     *      after `https://admin.googleapis.com/admin/directory/v1`
     *
     * @param array $request_data
     *      (Optional) Optional request data to send with the Google Workspace
     *      API GET request
     *
     * @return object
     */
    public function get(string $uri, array $request_data = []): object
    {
        // Append the Google Domain and Google Customer ID to the request data
        $request_data = array_merge($request_data, $this->required_parameters);

        // Get the initial GET response
        $response = Http::withToken($this->auth_token)
            ->withHeaders($this->request_headers)
            ->get(self::BASE_URL . $uri, $request_data);

        // Check if the data is paginated
        $isPaginated = $this->checkForPagination($response);

        // If the response is paginated
        if ($isPaginated) {
            // Get the paginated results
            $paginated_results = $this->getPaginatedResults(
                $uri,
                $request_data,
                $response
            );

            // The `$paginated_results` will be returned as an array of objects
            // which needs to be converted to a flat object for standardizing
            // the response returned. This needs to be a separate function
            // instead of casting to an object due to return body complexities
            // with nested array and object mixed notation.
            /** @phpstan-ignore-next-line */
            $response->paginated_results = $this->convertPaginatedResponseToObject($paginated_results);

            // Unset the body and json elements of the original Guzzle Response
            // Object. These will be reset with the paginated results.
            unset($response->body);
            unset($response->json);
        }

        // Parse the API response and return a Glamstack standardized response
        /** @phpstan-ignore-next-line */
        $parsed_api_response = $this->parseApiResponse($response, $isPaginated);

        $this->logResponse('get', self::BASE_URL . $uri, $parsed_api_response);

        // FIXME: Add connection config variable for throw exception. This should 
        // be able to fail silently and return error code in response and handled 
        // by the application. 
        if ($parsed_api_response->status->successful == false) {
            if (property_exists($parsed_api_response->object, 'error')) {
                abort($parsed_api_response->status->code, 'Google Workspace GET SDK Error. ' . $parsed_api_response->object->error_description);
            } else {
                abort(500, 'The Google Workspace SDK failed due to an unknown reason in the GET method.');
            }
        }

        return $parsed_api_response;
    }

    /**
     * Google Workspace API POST Request
     * 
     * Google will utilize POST request for inserting a new resource. This 
     * method is called from other services to perform a POST request and
     * return a structured object.
     *
     * Example Usage:
     * ```php
     * $google_workspace_api = new \Glamstack\GoogleWorkspace\ApiClient();
     * $google_workspace_api->post('/users/',
     *     [
     *         'name' => [
     *             'givenName' => 'Kate',
     *             'familyName' => 'Libby'
     *         ],
     *         'password' => 'ac!dBurnM3ss3sWithTheB4$t',
     *         'primaryEmail' => 'klibby@example.com'
     *     ]
     * );
     * ```
     *
     * Example Response:
     * ```php
     * {#1214
     *   +"headers": {
     *     +"ETag": (truncated)
     *     +"Content-Type": "application/json; charset=UTF-8"
     *     +"Vary": "Origin X-Origin Referer"
     *     +"Date": "Mon, 24 Jan 2022 17:35:55 GMT"
     *     +"Server": "ESF"
     *     +"Content-Length": "443"
     *     +"X-XSS-Protection": "0"
     *     +"X-Frame-Options": "SAMEORIGIN"
     *     +"X-Content-Type-Options": "nosniff"
     *     +"Alt-Svc": (truncated)
     *   }
     *   +"json": (truncated) // FIXME:
     *   +"object": {
     *     +"kind": "admin#directory#user"
     *     +"id": "115712261629077226469"
     *     +"etag": (truncated)
     *     +"primaryEmail": "klibby@example.com"
     *     +"name": {#1255
     *       +"givenName": "Kate"
     *       +"familyName": "Libby"
     *     }
     *     +"isAdmin": false
     *     +"isDelegatedAdmin": false
     *     +"creationTime": "2022-01-24T17:35:54.000Z"
     *     +"customerId": "C000nnnnn"
     *     +"orgUnitPath": "/"
     *     +"isMailboxSetup": false
     *   }
     *   +"status": {
     *     +"code": 200
     *     +"ok": true
     *     +"successful": true
     *     +"failed": false
     *     +"serverError": false
     *     +"clientError": false
     *   }
     * }
     * ```
     *
     * @param string $uri
     *      The URI of the Google Workspace API request with a leading slash
     *      after `https://admin.googleapis.com/admin/directory/v1`
     *
     * @param array $request_data
     *      (Optional) Optional request data to send with the Google Workspace
     *      API POST request
     *
     * @return object
     */
    public function post(string $uri, array $request_data = []): object
    {
        // Append to Google Domain and Google Customer ID to the request data
        $request_data = array_merge($request_data, $this->required_parameters);

        $request = Http::withToken($this->auth_token)
            ->withHeaders($this->request_headers)
            ->post(self::BASE_URL . $uri, $request_data);

        // Parse the API request's response and return a Glamstack response
        $response = $this->parseApiResponse($request);

        $this->logResponse('post', self::BASE_URL . $uri, $response);

        // FIXME: Add connection config variable for throw exception. This should 
        // be able to fail silently and return error code in response and handled 
        // by the application. 
        if ($response->status->successful == false) {
            if (property_exists($response->object, 'error')) {
                abort($response->status->code, 'Google Workspace POST SDK Error. ' . $response->object->error_description);
            } else {
                abort(500, 'The Google Workspace SDK failed due to an unknown reason in the POST method.');
            }
        }

        return $response;
    }

    /**
     * Google Workspace API PUT Request 
     * 
     * Google will utilize PUT request for updating an existing resource. This 
     * method is called from other services to perform a PUT request and return 
     * a structured object
     *
     * Example Usage:
     * ```php
     * $google_workspace_api = new \Glamstack\GoogleWorkspace\ApiClient();
     * $user_key = 'klibby@example.com';
     * $google_workspace_api->put('/users/' . $user_key,
     *     [
     *         name => [
     * .            'familyName' => 'Libby-Murphy'
     *         ]
     *     ]
     * );
     * ```
     *
     * Example Response:
     * ```php
     *    {#1271
     *   +"headers": {#1224
     *     +"ETag": (truncated)
     *     +"Content-Type": "application/json; charset=UTF-8"
     *     +"Vary": "Origin X-Origin Referer"
     *     +"Date": "Mon, 24 Jan 2022 17:45:47 GMT"
     *     +"Server": "ESF"
     *     +"Content-Length": "917"
     *     +"X-XSS-Protection": "0"
     *     +"X-Frame-Options": "SAMEORIGIN"
     *     +"X-Content-Type-Options": "nosniff"
     *     +"Alt-Svc": (truncated)
     *   }
     *   +"json": (truncated)
     *   +"object": {#1222
     *     +"kind": "admin#directory#user"
     *     +"id": "115712261629077226469"
     *     +"etag": (truncated)
     *     +"primaryEmail": "klibby@example.com"
     *     +"name": {#1255
     *       +"familyName": "Libby-Murphy"
     *     }
     *     (truncated)
     *   }
     *   +"status": {#1251
     *     +"code": 200
     *     +"ok": true
     *     +"successful": true
     *     +"failed": false
     *     +"serverError": false
     *     +"clientError": false
     *   }
     * }
     * ```
     *
     * @param string $uri
     *      The URI of the Google Workspace API request with a leading slash
     *      after `https://admin.googleapis.com/admin/directory/v1`
     *
     * @param array $request_data
     *      (Optional) Optional request data to send with the Google Workspace
     *      API PUT request
     *
     * @return object
     */
    public function put(string $uri, array $request_data = []): object
    {
        // Append to Google Domain and Google Customer ID to the request data
        $request_data = array_merge($request_data, $this->required_parameters);

        $request = Http::withToken($this->auth_token)
            ->withHeaders($this->request_headers)
            ->put(self::BASE_URL . $uri, $request_data);

        // Parse the API request's response and return a Glamstack standardized
        // response
        $response = $this->parseApiResponse($request);

        $this->logResponse('put', self::BASE_URL . $uri, $response);

        if ($response->status->successful == false) {
            if (property_exists($response->object, 'error')) {
                abort($response->status->code, 'Google Workspace PUT SDK Error. ' . $response->object->error_description);
            } else {
                abort(500, 'The Google Workspace SDK failed due to an unknown reason in the PUT method.');
            }
        }

        return $response;
    }

    /**
     * Google Workspace API DELETE Request. Google will utilize DELETE request
     * for removing an existing resource from the workspace.
     *
     * This method is called from other services to perform a DELETE request
     * and return a structured object.
     *
     * Example Usage:
     * ```php
     * $google_workspace_api = new \Glamstack\GoogleWorkspace\ApiClient();
     * $user_key = 'klibby@example.com';
     * $google_workspace_api->delete('/users/' . $user_key);
     * ```
     *
     * Example Response:
     * ```php
     * {#1255
     *   +"headers": {#1216
     *     +"ETag": (truncated)
     *     +"Vary": "Origin X-Origin Referer"
     *     +"Date": "Mon, 24 Jan 2022 17:50:04 GMT"
     *     +"Content-Type": "text/html"
     *     +"Server": "ESF"
     *     +"Content-Length": "0"
     *     +"X-XSS-Protection": "0"
     *     +"X-Frame-Options": "SAMEORIGIN"
     *     +"X-Content-Type-Options": "nosniff"
     *     +"Alt-Svc": (truncated)
     *   }
     *   +"json": "null"
     *   +"object": null
     *   +"status": {#1214
     *     +"code": 204
     *     +"ok": false
     *     +"successful": true
     *     +"failed": false
     *     +"serverError": false
     *     +"clientError": false
     *   }
     * }

     * ```
     *
     * @param string $uri
     *      The URI of the Google Workspace API request with a leading slash
     *      after `https://admin.googleapis.com/admin/directory/v1`
     *
     * @param array $request_data
     *      (Optional) Optional request data to send with the Google Workspace
     *      API DELETE request
     *
     * @return object
     */
    public function delete(string $uri, array $request_data = []): object
    {
        // Append to Google Domain and Google Customer ID to the request data
        $request_data = array_merge($request_data, $this->required_parameters);

        $request = Http::withToken($this->auth_token)
            ->withHeaders($this->request_headers)
            ->delete(self::BASE_URL . $uri, $request_data);

        // Parse the API request's response and return a Glamstack standardized
        // response
        $response = $this->parseApiResponse($request);

        $this->logResponse('delete', self::BASE_URL . $uri, $response);

        if ($response->status->successful == false) {
            if (property_exists($response->object, 'error')) {
                abort($response->status->code, 'Google Workspace DELETE SDK Error. ' . $response->object->error_description);
            } else {
                abort(500, 'The Google Workspace DELETE failed due to an unknown reason in the PUT method.');
            }
        }

        return $response;
    }

    /**
     * Check if pagination is used in the Google Workspace GET response.
     *
     * @param Response $response
     *      API response from Google Workspace GET request
     *
     * @return bool
     *      True if pagination is required | False if not
     */
    protected function checkForPagination(Response $response): bool
    {
        // Check if Google Workspace GET Request object contains `nextPageToken`
        if (property_exists($response->object(), 'nextPageToken')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Helper method for getting Google Workspace GET responses that require
     * pagination
     *
     * @param string $uri
     *      The URI of the Google Workspace API request with a leading slash
     *      after `https://admin.googleapis.com/admin/directory/v1`
     *
     * @param array $request_data
     *      Request data to send with the Google Workspace API GET request
     *
     * @param Response $response
     *      API response from Google Workspace GET request
     *
     * @return array
     */
    protected function getPaginatedResults(
        string $uri,
        array $request_data,
        Response $response
    ): array {

        // Initialize $records as an empty array. This is where we will store
        // the returned data from each paginated request.
        $records = [];

        // Collect the response body from the initial GET request's response
        $response_body = collect($this->getResponseBody($response))->flatten();

        // Merge the initial GET request's response into the $records array
        $records = array_merge($records, $response_body->toArray());

        // Get the next page using the initial responses `nextPageToken` element
        $next_response = $this->getNextPageResults(
            $uri,
            $request_data,
            $response
        );

        // Collect the response body from the subsequent GET request's response
        $next_response_body = collect(
            $this->getResponseBody($next_response)
        )->flatten();

        // Add the $next_response_body to the records array
        $records = array_merge($records, $next_response_body->toArray());

        // Check if there are more pages to GET
        $next_page_exists = $this->checkForPagination($next_response);

        // If there are more pages to GET
        if ($next_page_exists) {
            // Set the `$next_page_token` variable to the `$next_response`
            // `nextPageToken` element of the object
            $next_page_token = $this->getNextPageToken($next_response);
        }
        // Else there is not a third page of data and we no longer need to
        // proceed
        else {
            $next_page_token = null;
            // dd('setting next page token to null');
        }

        // If there is a third page then continue through all data until the
        // API response does not contain the `nextPageToken` element in the
        // returned object
        if ($next_page_token) {
            $next_response = $this->getNextPageResults(
                $uri,
                $request_data,
                $next_response
            );

            // Collect the response body from the subsequent GET request's response
            $next_response_body = collect(
                $this->getResponseBody($next_response)
            )->flatten();

            // Set the `next_response_body` to an array
            $next_response_body_array = $next_response_body->toArray();

            // Add the `next_response_body` array to the `records` array
            $records = array_merge($records, $next_response_body_array);

            // Check if there is another page
            $next_page_exists = $this->checkForPagination($next_response);

            // If there is another page set the `next_page_token` variable
            // to the `nextPageToken` from the response.
            if ($next_page_exists) {
                $next_page_token = $this->getNextPageToken($next_response);
            }
            // Else there is not another page so set the `next_page_token` to null
            else {
                $next_page_token = null;
            }
        }

        return $records;
    }

    /**
     * Helper method to get the `nextPageToken` element from the GET Response
     * object
     *
     * @see https://cloud.google.com/apis/design/design_patterns#list_pagination
     *
     * @param Response $response
     *      Google Workspace API GET Request Guzzle response
     *
     * @return string
     */
    protected function getNextPageToken(Response $response): string
    {
        $next_page_token = $response->object()->nextPageToken;
        return $next_page_token;
    }

    /**
     * Helper function to get the next page of a Google Workspace API GET
     * request.
     *
     * @param string $uri
     *      The URI of the Google Workspace API request with a leading slash
     *      after `https://admin.googleapis.com/admin/directory/v1`
     *
     * @param array $request_data
     *      Request data to send with the Google Workspace API GET request.
     *
     * @param Response $response
     *      API response from Google Workspace GET request
     *
     * @return Response
     */
    protected function getNextPageResults(
        string $uri,
        array $request_data,
        Response $response
    ): Response {

        // Set the Google Workspace Query parameter `pageToken` to the
        // responses `nextPageToken` element
        $next_page = [
            'pageToken' => $this->getNextPageToken($response)
        ];

        // Merge the `request_data` with the `next_page` this tells Google
        // Workspace that we are working with a paginated response
        $request_body = array_merge($request_data, $next_page);

        $records = Http::withToken($this->auth_token)
            ->withHeaders($this->request_headers)
            ->get(self::BASE_URL . $uri, $request_body);

        return $records;
    }

    /**
     * Helper method to get just the response data from the Response object
     *
     * @param Response $response
     *      API response from Google Workspace GET request
     *
     * @return object
     */
    protected function getResponseBody(Response $response): object
    {
        // Check if the response object contains the `nextPageToken` element
        $contains_next_page = $this->checkForPagination($response);

        // Get the response object
        $response_object = $response->object();

        // Unset unnecessary elements
        unset($response_object->kind);
        unset($response_object->etag);

        // If the response contains the `nextPageToken` element unset that
        if ($contains_next_page) {
            unset($response_object->nextPageToken);
        }

        return $response_object;
    }

    /**
     * Convert API Response Headers to Object
     * This method is called from the parseApiResponse method to prettify the
     * Guzzle Headers that are an array with nested array for each value, and
     * converts the single array values into strings and converts to an object
     * for easier and consistent accessibility with the parseApiResponse format.
     *
     * Example $header_response:
     * ```php
     * [
     *   "ETag" => [
     *     ""nMRgLWac8h8NyH7Uk5VvV4DiNp4uxXg5gNUd9YhyaJE/dky_PFyA8Zq0WLn1WqUCn_A8oes""
     *   ]
     *   "Content-Type" => [
     *     "application/json; charset=UTF-8"
     *   ]
     *   "Vary" => [
     *     "Origin"
     *     "X-Origin"
     *     "Referer"
     *   ]
     *   "Date" => [
     *      "Mon, 24 Jan 2022 15:39:46 GMT"
     *   ]
     *   "Server" => [
     *     "ESF"
     *   ]
     *   "Content-Length" => [
     *     "355675"
     *   ]
     *   "X-XSS-Protection" => [
     *     "0"
     *   ]
     *   "X-Frame-Options" => [
     *     "SAMEORIGIN"
     *   ]
     *   "X-Content-Type-Options" => [
     *     "nosniff"
     *   ]
     *   "Alt-Svc" => [
     *     (truncated)
     *   ]
     * ]
     * ```
     *
     * Example return object:
     * ```php
     * {#51667
     *   +"ETag": ""nMRgLWac8h8NyH7Uk5VvV4DiNp4uxXg5gNUd9YhyaJE/dky_PFyA8Zq0WLn1WqUCn_A8oes""
     *   +"Content-Type": "application/json; charset=UTF-8"
     *   +"Vary": "Origin X-Origin Referer"
     *   +"Date": "Mon, 24 Jan 2022 15:39:46 GMT"
     *   +"Server": "ESF"
     *   +"Content-Length": "355675"
     *   +"X-XSS-Protection": "0"
     *   +"X-Frame-Options": "SAMEORIGIN"
     *   +"X-Content-Type-Options": "nosniff"
     *   +"Alt-Svc": (truncated)
     * }
     * ```
     *
     * @param array $header_response
     *
     * @return object
     */
    protected function convertHeadersToObject(array $header_response): object
    {
        $headers = [];

        foreach ($header_response as $header_key => $header_value) {
            // if($header_key != '')
            $headers[$header_key] = implode(" ", $header_value);
        }

        return (object) $headers;
    }

    /**
     * Convert paginated API response array into an object
     *
     * @param array $paginatedResponse
     *      Combined object returns from multiple pages of API responses
     *
     * @return object
     *      Object of the API responses combined.
     */
    protected function convertPaginatedResponseToObject(
        array $paginatedResponse
    ): object {
        $results = [];

        foreach ($paginatedResponse as $response_key => $response_value) {
            $results[$response_key] = $response_value;
        }
        return (object) $results;
    }

    /**
     * Parse the API response and return custom formatted response for consistency
     *
     * Example Response:
     * ```php
     * {#1268
     *   +"headers": {#1216
     *     +"ETag": (truncated)
     *     +"Content-Type": "application/json; charset=UTF-8"
     *     +"Vary": "Origin X-Origin Referer"
     *     +"Date": "Mon, 24 Jan 2022 17:25:15 GMT"
     *     +"Server": "ESF"
     *     +"Content-Length": "1259"
     *     +"X-XSS-Protection": "0"
     *     +"X-Frame-Options": "SAMEORIGIN"
     *     +"X-Content-Type-Options": "nosniff"
     *     +"Alt-Svc": (truncated)
     *   }
     *   +"json": (truncated)
     *   +"object": {#1251
     *     +"kind": "admin#directory#user"
     *     +"id": "114522752583947996869"
     *     +"etag": (truncated)
     *     +"primaryEmail": "klibby@example.com"
     *     +"name": {#1248
     *       +"givenName": "Kate"
     *       +"familyName": "Libby"
     *       +"fullName": "Kate Libby"
     *     }
     *     +"isAdmin": true
     *     +"isDelegatedAdmin": false
     *     +"lastLoginTime": "2022-01-21T17:44:13.000Z"
     *     +"creationTime": "2021-12-08T13:15:43.000Z"
     *     +"agreedToTerms": true
     *     +"suspended": false
     *     +"archived": false
     *     +"changePasswordAtNextLogin": false
     *     +"ipWhitelisted": false
     *     +"emails": array:3 [
     *       0 => {#1260
     *         +"address": "klibby@example.com"
     *         +"type": "work"
     *       }
     *       1 => {#1259
     *         +"address": "klibby@example-test.com"
     *         +"primary": true
     *       }
     *       2 => {#1255
     *         +"address": "klibby@example.com.test-google-a.com"
     *       }
     *     ]
     *     +"phones": array:1 [
     *       0 => {#1214
     *         +"value": "5555555555"
     *         +"type": "work"
     *       }
     *     ]
     *     +"languages": array:1 [
     *       0 => {#1271
     *         +"languageCode": "en"
     *         +"preference": "preferred"
     *       }
     *     ]
     *     +"nonEditableAliases": array:1 [
     *       0 => "klibby@example.com.test-google-a.com"
     *     ]
     *     +"customerId": "C000nnnnn"
     *     +"orgUnitPath": "/"
     *     +"isMailboxSetup": true
     *     +"isEnrolledIn2Sv": false
     *     +"isEnforcedIn2Sv": false
     *     +"includeInGlobalAddressList": true
     *   }
     *   +"status": {#1269
     *     +"code": 200
     *     +"ok": true
     *     +"successful": true
     *     +"failed": false
     *     +"serverError": false
     *     +"clientError": false
     *   }
     * }
     * ```
     *
     * @see https://laravel.com/docs/8.x/http-client#making-requests
     *
     * @param object $response
     *      Response object from API results
     *
     * @param false $paginated
     *      If the response is paginated or not
     *
     * @return object
     *      Custom response returned for consistency
     */
    protected function parseApiResponse(object $response, bool $paginated = false): object
    {
        return (object) [
            'headers' => $this->convertHeadersToObject($response->headers()),
            'json' => $paginated == true ? json_encode($response->paginated_results) : json_encode($response->json()),
            'object' => $paginated == true ? (object) $response->paginated_results : $response->object(),
            'status' => (object) [
                'code' => $response->status(),
                'ok' => $response->ok(),
                'successful' => $response->successful(),
                'failed' => $response->failed(),
                'serverError' => $response->serverError(),
                'clientError' => $response->clientError(),
            ],
        ];
    }
}
