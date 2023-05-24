<?php

namespace GitlabIt\GoogleWorkspace\Tests;

use GitlabIt\GoogleWorkspace\ApiClientServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        //        ini_set('memory_limit', '48M');
        if (!is_dir(__DIR__ . '/../vendor/orchestra/testbench-core/laravel/storage/keys')) {
            mkdir(__DIR__ . '/../vendor/orchestra/testbench-core/laravel/storage/keys');
        }
        if (!is_dir(__DIR__ . '/../vendor/orchestra/testbench-core/laravel/storage/keys/google-workspace-sdk')) {
            mkdir(__DIR__ . '/../vendor/orchestra/testbench-core/laravel/storage/keys/google-workspace-sdk');
        }
        if (!is_link(__DIR__ . '/../vendor/orchestra/testbench-core/laravel/storage/keys/google-workspace-sdk/test.json')) {
            symlink(__DIR__ . '/../storage/keys/google-workspace-sdk/test.json', __DIR__ . '/../vendor/orchestra/testbench-core/laravel/storage/keys/google-workspace-sdk/test.json');
        }
        if (!is_link(__DIR__ . '/../vendor/orchestra/testbench-core/laravel/storage/keys/google-workspace-sdk/prod.json')) {
            symlink(__DIR__ . '/../storage/keys/google-workspace-sdk/prod.json', __DIR__ . '/../vendor/orchestra/testbench-core/laravel/storage/keys/google-workspace-sdk/prod.json');
        }

        if (!is_link(__DIR__ . '/../vendor/orchestra/testbench-core/laravel/composer.lock')) {
            symlink(__DIR__ . '/../composer.lock', __DIR__ . '/../vendor/orchestra/testbench-core/laravel/composer.lock');
        }
    }

    protected function getPackageProviders($app)
    {
        return [
            ApiClientServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
    }
}
