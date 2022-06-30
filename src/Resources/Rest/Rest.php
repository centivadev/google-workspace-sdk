<?php

namespace Glamstack\GoogleWorkspace\Resources\Rest;

use Exception;
use Glamstack\GoogleWorkspace\ApiClient;

class Rest extends ApiClient
{
    /**
     * GET HTTP Request
     *
     * This will perform a GET request against the provided `uri`. There is no
     * validation for the provided URI or request data in this method.
     *
     * @param string $uri
     *      The Google URI to run the GET request with
     *
     * @param array $request_data
     *      Request data to load into GET request `Request Body`
     *
     * @return object|string
     *      Example Response:
     *      ```php
     *      {
     *        +"headers": {
     *          +"ETag": (truncated)
     *          +"Content-Type": "application/json; charset=UTF-8"
     *          +"Vary": "Origin X-Origin Referer"
     *          +"Date": "Mon, 24 Jan 2022 17:25:15 GMT"
     *          +"Server": "ESF"
     *          +"Content-Length": "1259"
     *          +"X-XSS-Protection": "0"
     *          +"X-Frame-Options": "SAMEORIGIN"
     *          +"X-Content-Type-Options": "nosniff"
     *          +"Alt-Svc": (truncated)
     *        }
     *        +"json": (truncated) // FIXME
     *        +"object": {
     *          +"kind": "admin#directory#user"
     *          +"id": "114522752583947996869"
     *          +"etag": (truncated)
     *          +"primaryEmail": "klibby@example.com"
     *          +"name": {#1248
     *            +"givenName": "Kate"
     *            +"familyName": "Libby"
     *            +"fullName": "Kate Libby"
     *          }
     *          +"isAdmin": true
     *          (truncated)
     *        }
     *        +"status": {
     *          +"code": 200
     *          +"ok": true
     *          +"successful": true
     *          +"failed": false
     *          +"serverError": false
     *          +"clientError": false
     *        }
     *      }
     * ```
     * @throws Exception
     */
    public function get(string $uri, array $request_data = []): object|string
    {
        $method = new Method($this);
        return $method->get($uri, $request_data);
    }

    /**
     * POST HTTP Request
     *
     * This will perform a POST request against the provided `uri`. There is no
     * validation for the provided URI or request data in this method.
     *
     * @param string $uri
     *      The Google URI to run the POST request with
     *
     * @param array|null $request_data
     *      Request data to load into POST request `Request Body`
     *
     * @return object|string
     *      Example Response:
     *      ```php
     *      {#1214
     *        +"headers": {
     *          +"ETag": (truncated)
     *          +"Content-Type": "application/json; charset=UTF-8"
     *          +"Vary": "Origin X-Origin Referer"
     *          +"Date": "Mon, 24 Jan 2022 17:35:55 GMT"
     *          +"Server": "ESF"
     *          +"Content-Length": "443"
     *          +"X-XSS-Protection": "0"
     *          +"X-Frame-Options": "SAMEORIGIN"
     *          +"X-Content-Type-Options": "nosniff"
     *          +"Alt-Svc": (truncated)
     *        }
     *        +"json": (truncated) // FIXME:
     *        +"object": {
     *          +"kind": "admin#directory#user"
     *          +"id": "115712261629077226469"
     *          +"etag": (truncated)
     *          +"primaryEmail": "klibby@example.com"
     *          +"name": {#1255
     *            +"givenName": "Kate"
     *            +"familyName": "Libby"
     *          }
     *          +"isAdmin": false
     *          +"isDelegatedAdmin": false
     *          +"creationTime": "2022-01-24T17:35:54.000Z"
     *          +"customerId": "C000nnnnn"
     *          +"orgUnitPath": "/"
     *          +"isMailboxSetup": false
     *        }
     *        +"status": {
     *          +"code": 200
     *          +"ok": true
     *          +"successful": true
     *          +"failed": false
     *          +"serverError": false
     *          +"clientError": false
     *        }
     *      }
     *      ```
     *
     * @throws Exception
     */
    public function post(string $uri, ?array $request_data = []): object|string
    {
        $method = new Method($this);
        return $method->post($uri, $request_data);
    }

    /**
     * PATCH HTTP Request
     *
     * This will perform a PATCH request against the provided `uri`. There is no
     * validation for the provided URI or request data in this method.
     *
     * @param string $uri
     *      The Google URI to run the PATCH request with
     *
     * @param array $request_data
     *      Request data to load into PATCH request `Request Body`
     *
     * @return object|string
     *      Example Response:
     *      ```php
     *      {#1214
     *        +"headers": {
     *          +"ETag": (truncated)
     *          +"Content-Type": "application/json; charset=UTF-8"
     *          +"Vary": "Origin X-Origin Referer"
     *          +"Date": "Mon, 24 Jan 2022 17:35:55 GMT"
     *          +"Server": "ESF"
     *          +"Content-Length": "443"
     *          +"X-XSS-Protection": "0"
     *          +"X-Frame-Options": "SAMEORIGIN"
     *          +"X-Content-Type-Options": "nosniff"
     *          +"Alt-Svc": (truncated)
     *        }
     *        +"json": (truncated) // FIXME:
     *        +"object": {
     *          +"kind": "admin#directory#user"
     *          +"id": "115712261629077226469"
     *          +"etag": (truncated)
     *          +"primaryEmail": "klibby@example.com"
     *          +"name": {#1255
     *            +"givenName": "Kate"
     *            +"familyName": "Libby"
     *          }
     *          +"isAdmin": false
     *          +"isDelegatedAdmin": false
     *          +"creationTime": "2022-01-24T17:35:54.000Z"
     *          +"customerId": "C000nnnnn"
     *          +"orgUnitPath": "/"
     *          +"isMailboxSetup": false
     *        }
     *        +"status": {
     *          +"code": 200
     *          +"ok": true
     *          +"successful": true
     *          +"failed": false
     *          +"serverError": false
     *          +"clientError": false
     *        }
     *      }
     * ```
     *
     * @throws Exception
     */
    public function patch(string $uri, array $request_data = []): object|string
    {
        $method = new Method($this);
        return $method->patch($uri, $request_data);
    }

    /**
     * PUT HTTP Request
     *
     * This will perform a PUT request against the provided `uri`. There is no
     * validation for the provided URI or request data in this method.
     *
     * @param string $uri
     *      The Google URI to run the PUT request with
     *
     * @param array $request_data
     *      Request data to load into PUT request `Request Body`
     *
     * @return object|string
     *      Example Response:
     *      ```php
     *         {#1271
     *        +"headers": {#1224
     *          +"ETag": (truncated)
     *          +"Content-Type": "application/json; charset=UTF-8"
     *          +"Vary": "Origin X-Origin Referer"
     *          +"Date": "Mon, 24 Jan 2022 17:45:47 GMT"
     *          +"Server": "ESF"
     *          +"Content-Length": "917"
     *          +"X-XSS-Protection": "0"
     *          +"X-Frame-Options": "SAMEORIGIN"
     *          +"X-Content-Type-Options": "nosniff"
     *          +"Alt-Svc": (truncated)
     *        }
     *        +"json": (truncated)
     *        +"object": {#1222
     *          +"kind": "admin#directory#user"
     *          +"id": "115712261629077226469"
     *          +"etag": (truncated)
     *          +"primaryEmail": "klibby@example.com"
     *          +"name": {#1255
     *            +"familyName": "Libby-Murphy"
     *          }
     *          (truncated)
     *        }
     *        +"status": {#1251
     *          +"code": 200
     *          +"ok": true
     *          +"successful": true
     *          +"failed": false
     *          +"serverError": false
     *          +"clientError": false
     *        }
     *      }
     * ```

     *
     * @throws Exception
     */
    public function put(string $uri, array $request_data = []): object|string
    {
        $method = new Method($this);
        return $method->put($uri, $request_data);
    }

    /**
     * DELETE HTTP Request
     *
     * This will perform a DELETE request against the provided `uri`. There is no
     * validation for the provided URI or request data in this method.
     *
     * @param string $uri
     *      The Google URI to run the DELETE request with
     *
     * @param array $request_data
     *      Request data to load into DELETE request `Request Body`
     *
     * @return object|string
     *      Example Response:
     *      ```php
     *      {#1255
     *        +"headers": {#1216
     *          +"ETag": (truncated)
     *          +"Vary": "Origin X-Origin Referer"
     *          +"Date": "Mon, 24 Jan 2022 17:50:04 GMT"
     *          +"Content-Type": "text/html"
     *          +"Server": "ESF"
     *          +"Content-Length": "0"
     *          +"X-XSS-Protection": "0"
     *          +"X-Frame-Options": "SAMEORIGIN"
     *          +"X-Content-Type-Options": "nosniff"
     *          +"Alt-Svc": (truncated)
     *        }
     *        +"json": "null"
     *        +"object": null
     *        +"status": {#1214
     *          +"code": 204
     *          +"ok": false
     *          +"successful": true
     *          +"failed": false
     *          +"serverError": false
     *          +"clientError": false
     *        }
     *      }
     *      ```
     *
     * @throws Exception
     */
    public function delete(string $uri, array $request_data = []): object|string
    {
        $method = new Method($this);
        return $method->delete($uri, $request_data);
    }
}
