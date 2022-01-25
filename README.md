# Google Workspace SDK

## Overview

The Google Workspace SDK is an open source [Composer](https://getcomposer.org/) package created by [GitLab IT Engineering](https://about.gitlab.com/handbook/business-technology/engineering/) for use in the [GitLab Access Manager](https://gitlab.com/gitlab-com/business-technology/engineering/access-manager) Laravel application for connecting to Google API endpoints for provisioning and deprovisioning of users, groups, group membership, and other related functionality.

> **Disclaimer:** This is not an official package maintained by the Google or GitLab product and development teams. This is an internal tool that we use in the GitLab IT department that we have open sourced as part of our company values.
>
> Please use at your own risk and create issues for any bugs that you encounter.
>
> We do not maintain a roadmap of community feature requests, however we invite you to contribute and we will gladly review your merge requests.

## Dependencies

**Note:** This package will require the `glamstack/google-auth-sdk` package in order to operate. This is already configured as a required package in the composer.json file and should be automatically loaded when installing this package.

> All configurations for this package will be configured under the `glamstack-google-config.php` file that will be loaded when the `glamstack/google-auth-sdk` package is installed. For further guidance please see the [Glamstack/google-auth-sdk README.md](https://gitlab.com/gitlab-com/business-technology/engineering/access-manager/packages/composer/google-auth-sdk/-/blob/main/README.md)

### Maintainers

| Name                                                                   | GitLab Handle                                          |
| ---------------------------------------------------------------------- | ------------------------------------------------------ |
| [Dillon Wheeler](https://about.gitlab.com/company/team/#dillonwheeler) | [@dillonwheeler](https://gitlab.com/dillonwheeler)     |
| [Jeff Martin](https://about.gitlab.com/company/team/#jeffersonmartin)  | [@jeffersonmartin](https://gitlab.com/jeffersonmartin) |

### How It Works

The package will utilize the [glamstack/google-auth-sdk](https://gitlab.com/gitlab-com/business-technology/engineering/access-manager/packages/composer/gitlab-sdk) package for creating the [Google JWT Web Token](https://cloud.google.com/iot/docs/how-tos/credentials/jwts) to authenticate with [Google Workspace API's](https://developers.google.com/admin-sdk/directory/reference/rest#service:-admin.googleapis.com).

For more information on the required configuration for [glamstack/google-auth-sdk](https://gitlab.com/gitlab-com/business-technology/engineering/access-manager/packages/composer/gitlab-sdk) please see the [Google Auth SDK README.md](https://gitlab.com/gitlab-com/business-technology/engineering/access-manager/packages/composer/google-auth-sdk/-/blob/main/README.md).

This package is not intended to provide functions for every endpoint for [Google Workspace API's](https://developers.google.com/admin-sdk/directory/reference/rest#service:-admin.googleapis.com).

We have taken a simpler approach by providing a universal ApiClient that can perform GET, POST, PUT, and DELETE requests to any endpoint that you find in the [Google Workspace API's](https://developers.google.com/admin-sdk/directory/reference/rest#service:-admin.googleapis.com) documentation and handles the API response, error handling, and pagination for you.

This builds upon the simplicity of the Laravel HTTP Client that is powered by the Guzzle HTTP client to provide "last lines of code parsing" for [Google Workspace API's](https://developers.google.com/admin-sdk/directory/reference/rest#service:-admin.googleapis.com) responses to improve the developer experience.

We have additional classes and methods for the endpoints that GitLab Access Manager uses frequently that we will iterate upon over time.

```php
$google_workspace_api = new \Glamstack\GoogleWorkspace\ApiClient();

// Retrieves a paginated list of either deleted users or all users in a domain.
// https://developers.google.com/admin-sdk/directory/reference/rest/v1/users/list
$records = $google_workspace_api->get('/users');

// Retrieves a paginated list of either deleted users or all users in a domain
// with query parameters included.
// https://developers.google.com/admin-sdk/directory/reference/rest/v1/users/list
// https://developers.google.com/admin-sdk/directory/reference/rest/v1/users/list#OrderBy
// https://developers.google.com/admin-sdk/directory/v1/guides/search-users
$records = $google_workspace_api->get('/users',[
    'maxResults' => '200',
    'orderBy' => 'EMAIL',
    'query' => [
        'orgDepartment' => 'Test Department'
    ],
]);

// Get a specific user from Google Workspace
// https://developers.google.com/admin-sdk/directory/reference/rest/v1/users/get
$record = $google_workspace_api->get('/users/'.$userKey);

// Create new Google Workspace User
// https://developers.google.com/admin-sdk/directory/reference/rest/v1/users/insert
// https://developers.google.com/admin-sdk/directory/reference/rest/v1/users#User
$record = $google_workspace_api->post('/users', [
    'name' => [
            'familyName' => 'LastName',
            'givenName' => 'FirstName'
        ],
    'password' => 'randomLongSecurePa$$word',
    'primaryEmail' => 'firstname_lastname@example.com'
]);

// Update an existing Google Workspace User
// https://developers.google.com/admin-sdk/directory/reference/rest/v1/users/update
$record = $google_workspace_api->put('/users/'.$userKey, [
    'name' => [
        'givenName' => 'NewLastName'
    ]
]);

// Delete a Google Workspace User
// https://developers.google.com/admin-sdk/directory/reference/rest/v1/users/delete
$record = $google_workspace_api->delete('/users/'.$userKey);
```

## Installation

### Requirements

| Requirement | Version |
| ----------- | ------- |
| PHP         | >=8.0   |
| Laravel     | >=8.0   |

### Add Composer Package

```bash
composer require glamstack/google-workspace-sdk
```

> If you are contributing to this package, see [CONTRIBUTING](CONTRIBUTING.md) for instructions on configuring a local composer package with symlinks.

### Custom Logging Configuration

By default, we use the `single` channel for all logs that is configured in your application's `config/logging.php` file. This sends all Google Workspace log messages to the `storage/logs/laravel.log` file.

If you would like to see Google Workspace logs in a separate log file that is easier to triage without unrelated log messages, you can create a custom log channel.  For example, we recommend using the value of `glamstack-google-workspace`, however you can choose any name you would like.

Add the custom log channel to `config/logging.php`.

```php
    'channels' => [

        // Add anywhere in the `channels` array

        'glamstack-google-workspace' => [
            'name' => 'glamstack-google-workspace',
            'driver' => 'single',
            'level' => 'debug',
            'path' => storage_path('logs/glamstack-google-workspace.log'),
        ],
    ],
```

Update the `channels.stack.channels` array to include the array key (ex.  `glamstack-google-workspace`) of your custom channel. Be sure to add `glamstack-google-workspace` to the existing array values and not replace the existing values.

```php
    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single','slack', 'glamstack-google-workspace'],
            'ignore_exceptions' => false,
        ],
    ],
```

## API Request

You can make an API request to any of the resource endpoints in the [Google Workspace Admin SDK Directory Documentation](https://developers.google.com/admin-sdk/directory/reference/rest).

### Inline Usage

```php
// Initialize the SDK
$google_workspace_api = new \Glamstack\GoogleWorkspace\ApiClient();
```

### GET Request

The endpoints start with a leading `/` after `https://admin.googleapis.com/admin/directory/v1`. The Google Workspace API documentation provides the endpoint verbatim.

For examples, the [List Google Workspace Users](https://developers.google.com/admin-sdk/directory/reference/rest/v1/users/list) API Documentation shows the endpoint

```bash
GET https://admin.googleapis.com/admin/directory/v1/users
```

With the SDK, you use the get() method with the endpoint /users as the first argument.

```php
$google_workspace_api->get('/users');
```

You can also use variables or database models to get data for constructing your endpoints.

```php
$endpoint = '/users';
$records = $google_workspace_api->get($endpoint);
```

Here are some more examples of using endpoints.

```php
// Get a list of Google Workspace Users
// https://developers.google.com/admin-sdk/directory/reference/rest/v1/users/list
$records = $google_workspace_api->get('/users');

// Get a specific Google Workspace User
// https://developers.google.com/admin-sdk/directory/reference/rest/v1/users/get
$record = $google_workspace_api->get('/users/'.$userKey);
```

### GET Requests with Query String Parameters

The second argument of a `get()` method is an optional array of parameters that is parsed by the SDK and the [Laravel HTTP Client](https://laravel.com/docs/8.x/http-client#get-request-query-parameters) and rendered as a query string with the `?` and `&` added automatically.

```php
// Retrieves a paginated list of either deleted users or all users in a domain
// with query parameters included.
// https://developers.google.com/admin-sdk/directory/reference/rest/v1/users/list
// https://developers.google.com/admin-sdk/directory/reference/rest/v1/users/list#OrderBy
// https://developers.google.com/admin-sdk/directory/v1/guides/search-users
$records = $google_workspace_api->get('/users',[
    'maxResults' => '200',
    'orderBy' => 'EMAIL',
]);

// This will parse the array and render the query string
// https://admin.googleapis.com/admin/directory/v1/users?maxResults='200'&orderBy='EMAIL'
```

### POST Requests

The `post()` method works almost identically to a `get()` request with an array of parameters, however the parameters are passed as form data using the `application/json` content type rather than in the URL as a query string. This is industry standard and not specific to the SDK.

You can learn more about request data in the [Laravel HTTP Client documentation](https://laravel.com/docs/8.x/http-client#request-data).

```php
// Create new Google Workspace User
// https://developers.google.com/admin-sdk/directory/reference/rest/v1/users/insert
// https://developers.google.com/admin-sdk/directory/reference/rest/v1/users#User
$record = $google_workspace_api->post('/users', [
    'name' => [
            'familyName' => 'LastName',
            'givenName' => 'FirstName'
        ],
    'password' => 'randomLongSecurePa$$word',
    'primaryEmail' => 'firstname_lastname@example.com'
]);
```

### PUT Requests

The `put()` method is used for updating an existing record (similar to `PATCH` requests). You need to ensure that the ID of the record that you want to update is provided in the first argument (URI).

In most applications, this will be a variable that you get from your database or another location and won't be hard-coded.

```php
// Update an existing Google Workspace User
// https://developers.google.com/admin-sdk/directory/reference/rest/v1/users/update
$record = $google_workspace_api->put('/users/'.$userKey, [
    'name' => [
        'givenName' => 'NewLastName'
    ]
]);
```

### DELETE Requests

The `delete()` method is used for methods that will destroy the resource based on the ID that you provide.

Keep in mind that `delete()` methods will return different status codes depending on the vendor (ex. 200, 201, 202, 204, etc). Google Workspace API's will return a `204` status code for successfully deleted resources.

```php
// Delete a Google Workspace User
// https://developers.google.com/admin-sdk/directory/reference/rest/v1/users/delete
$record = $google_workspace_api->delete('/users/'.$userKey);
```

### Class Methods

The examples above show basic inline usage that is suitable for most use cases. If you prefer to use classes and constructors, the example below will provide a helpful example.

```php
<?php

use Glamstack\GoogleWorkspace\ApiClient;

class GoogleWorkspaceUserService
{
    protected $google_workspace_api;

    public function __construct()
    {
        $this->google_workspace_api = new \Glamstack\GoogleWorkspace\ApiClient();
    }

    public function listUsers(array $query = []) : object
    {
        $users = $this->google_workspace_api->get('/users', $query);
        return $users->object
    }

    public function getUser(string $user_key, array $query = []) : object
    {
        $user = $this->google_workspace_api->get('/users/'.$user_key, $query);
        return $user->object;
    }

    public function storeUser(string $user_key, array $request_data = []) : object
    {
       $response = $this->google_workspace_api->post('/users/'.$user_key, $request_data);
       return $response->object;
    }

    public function updateUser(string $user_key, array $request_data = []) : object
    {
        $response = $this->google_workspace_api->put('/users/'.$user_key, $request_data);
        return $response->object;
    }

    public function deleteUser(string $user_key) : bool
    {
        $response = $this->google_workspace_api->delete('/users/'.$user_key);
        return $response->status->successful;
    }
}
```

## API Responses

This SDK uses the GLAM Stack standards for API response formatting.

```php
// API Request
$response = $this->google_workspace_api->get('/users/'.$user_key);

// API Response
$response->headers; // object
$response->json; // json
$response->object; // object
$response->status; // object
$response->status->code; // int (ex. 200)
$response->status->ok; // bool
$response->status->successful; // bool
$response->status->failed; // bool
$response->status->serverError; // bool
$response->status->clientError; // bool
```

#### API Response Headers

```php
$response = $this->google_workspace_api->get('/projects/'.$user_key);
$response->headers;
```

```json
{
    +"ETag": ""nMRgLWac8h8NyH7Uk5VvV4DiNp4uxXg5gNUd9YhyaJE/MgKWL9SwIVWCY7rRA988mR8yR-k""
    +"Content-Type": "application/json; charset=UTF-8"
    +"Vary": "Origin X-Origin Referer"
    +"Date": "Thu, 20 Jan 2022 16:36:03 GMT"
    +"Server": "ESF"
    +"Content-Length": "1257"
    +"X-XSS-Protection": "0"
    +"X-Frame-Options": "SAMEORIGIN"
    +"X-Content-Type-Options": "nosniff"
    +"Alt-Svc": "h3=":443"; ma=2592000,h3-29=":443"; ma=2592000,h3-Q050=":443"; ma=2592000,h3-Q046=":443"; ma=2592000,h3-Q043=":443"; ma=2592000,quic=":443"; ma=2592000; v="46,43""
}
```

#### API Response Specific Header

```php
$headers = (array) $response->headers;
$content_type = $headers['Content-Type'];
```

```bash
application/json
```

#### API Response JSON

```php
$response = $this->google_workspace_api->get('/projects/'.$user_key);
$response->json;
```

```json
{"kind":"admin#directory#user","id":"114522752583947996869","etag":"\"nMRgLWac8h8NyH7Uk5VvV4DiNp4uxXg5gNUd9YhyaJE\/MgKWL9SwIVWCY7rRA988mR8yR-k\"","primaryEmail":"dwheeler@gitlab-test.com","name":{"givenName":"Dillon","familyName":"Wheeler","fullName":"Dillon Wheeler"},"isAdmin":true,"isDelegatedAdmin":false,"lastLoginTime":"2022-01-18T15:26:16.000Z","creationTime":"2021-12-08T13:15:43.000Z","agreedToTerms":true,"suspended":false,"archived":false,"changePasswordAtNextLogin":false,"ipWhitelisted":false,"emails":[{"address":"dwheeler@gitlab-test.com","type":"work"},{"address":"dwheeler@gitlab-test.com","primary":true},{"address":"dwheeler@gitlab-test.com.test-google-a.com"}],"phones":[{"value":"5555555555","type":"work"}],"languages":[{"languageCode":"en","preference":"preferred"}],"nonEditableAliases":["dwheeler@gitlab-test.com.test-google-a.com"],"customerId":"C000aaaaa","orgUnitPath":"\/","isMailboxSetup":true,"isEnrolledIn2Sv":false,"isEnforcedIn2Sv":false,"includeInGlobalAddressList":true}"
```

#### API Response Object

```php
$response = $this->google_workspace_api->get('/projects/'.$user_key);
$response->object;
```

```php
{#1256
  +"kind": "admin#directory#user"
  +"id": "114522752583947996869"
  +"etag": ""nMRgLWac8h8NyH7Uk5VvV4DiNp4uxXg5gNUd9YhyaJE/MgKWL9SwIVWCY7rRA988mR8yR-k""
  +"primaryEmail": "dwheeler@gitlab-test.com"
  +"name": {#1242
    +"givenName": "Dillon"
    +"familyName": "Wheeler"
    +"fullName": "Dillon Wheeler"
  }
  +"isAdmin": true
  +"isDelegatedAdmin": false
  +"lastLoginTime": "2022-01-18T15:26:16.000Z"
  +"creationTime": "2021-12-08T13:15:43.000Z"
  +"agreedToTerms": true
  +"suspended": false
  +"archived": false
  +"changePasswordAtNextLogin": false
  +"ipWhitelisted": false
  +"emails": array:3 [
    0 => {#1253
      +"address": "dwheeler@gitlab.com"
      +"type": "work"
    }
    1 => {#1258
      +"address": "dwheeler@gitlab-test.com"
      +"primary": true
    }
    2 => {#1259
      +"address": "dwheeler@gitlab-test.com.test-google-a.com"
    }
  ]
  +"phones": array:1 [
    0 => {#1247
      +"value": "5555555555"
      +"type": "work"
    }
  ]
  +"languages": array:1 [
    0 => {#1250
      +"languageCode": "en"
      +"preference": "preferred"
    }
  ]
  +"nonEditableAliases": array:1 [
    0 => "dwheeler@gitlab-test.com.test-google-a.com"
  ]
  +"customerId": "C000aaaaa"
  +"orgUnitPath": "/"
  +"isMailboxSetup": true
  +"isEnrolledIn2Sv": false
  +"isEnforcedIn2Sv": false
  +"includeInGlobalAddressList": true
}
```

#### API Response Status

See the [Laravel HTTP Client documentation](https://laravel.com/docs/8.x/http-client#error-handling) to learn more about the different status booleans.

```php
$response = $this->google_workspace_api->get('/projects/'.$user_key);
$response->status;
```

```php
{
  +"code": 200
  +"ok": true
  +"successful": true
  +"failed": false
  +"serverError": false
  +"clientError": false
}
```

#### API Response Status Code

```php
$response = $this->google_workspace_api->get('/projects/'.$user_key);
$response->status->code;
```

```bash
200
```

## Error Handling

The HTTP status code for the API response is included in each log entry in the message and in the JSON `status_code`. Any internal SDK errors also included an equivalent status code depending on the type of error. The `message` includes the SDK friendly message. If an exception is thrown, the `reference`

If a `5xx` error is returned from the API, the `ApiClient` `handleException` method will return a response.

See the [Log Outputs](#log-outputs) below for how the SDK handles errors and logging.

## Log Outputs

> The output of error messages is shown in the `README` to allow search engines to index these messages for developer debugging support. Any 5xx error messages will be returned as as `Symfony\Component\HttpKernel\Exception\HttpException` or configuration errors, including any errors in the `ApiClient::setApiConnectionVariables()` method.

## Issue Tracking and Bug Reports

Please visit our [issue tracker](https://gitlab.com/gitlab-com/business-technology/engineering/access-manager/packages/composer/google-auth-sdk/-/issues) and create an issue or comment on an existing issue.

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) to learn more about how to contribute.-
