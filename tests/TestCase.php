<?php

namespace MarkWalet\TestableRequests\Tests;

use Illuminate\Contracts\Foundation\Application;
use MarkWalet\TestableRequests\TestFormRequest;
use MarkWalet\TestableRequests\ValidatesRequests;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function makeRequest(string $requestClass, array $headers = [], string $method = 'POST'): TestFormRequest
    {
        $requestFactory = new class($this) {
            use ValidatesRequests;

            public function __construct(private readonly Orchestra $testCase)
            {
                $this->app = $testCase->application();
                $this->serverVariables = [];
            }

            public function make(string $requestClass, array $headers = [], string $method = 'POST'): TestFormRequest
            {
                return $this->createRequest($requestClass, $headers, $method);
            }

            protected function prepareUrlForRequest($uri): string
            {
                return $this->testCase->makeUrl($uri);
            }

            protected function prepareCookiesForRequest(): array
            {
                return [];
            }

            protected function transformHeadersToServerVars(array $headers): array
            {
                $server = [];

                foreach ($headers as $key => $value) {
                    $key = strtoupper(str_replace('-', '_', $key));

                    if (! in_array($key, ['CONTENT_TYPE', 'REMOTE_ADDR'], true)) {
                        $key = 'HTTP_'.$key;
                    }

                    $server[$key] = $value;
                }

                return $server;
            }
        };

        return $requestFactory->make($requestClass, $headers, $method);
    }

    public function makeUrl(string $uri): string
    {
        return $uri;
    }

    public function application(): Application
    {
        return $this->app;
    }
}
