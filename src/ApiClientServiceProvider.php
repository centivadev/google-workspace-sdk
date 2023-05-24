<?php

namespace GitlabIt\GoogleWorkspace;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ApiClientServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('google-workspace-sdk')
            ->hasConfigFile(['google-workspace-sdk', 'tests']);
    }
}
