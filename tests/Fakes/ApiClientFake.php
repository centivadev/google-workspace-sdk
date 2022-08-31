<?php

namespace Glamstack\GoogleWorkspace\Tests\Fakes;

use Glamstack\GoogleWorkspace\ApiClient;

class ApiClientFake extends ApiClient
{

    public function setConnectionKey(?string $connection_key): void
    {
        parent::setConnectionKey($connection_key); // TODO: Change the autogenerated stub
    }

    public function setRequestHeaders(): void
    {
        $this->request_headers = [
            'User-Agent' => 'google-workspace-sdk/dev/php8.1'
        ];
    }
}