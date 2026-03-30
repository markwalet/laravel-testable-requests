<?php

namespace MarkWalet\TestableRequests\Tests;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Testing\TestResponse;
use MarkWalet\TestableRequests\TestFormRequest;
use MarkWalet\TestableRequests\ValidatesRequests;
use Orchestra\Testbench\TestCase as Orchestra;
use Symfony\Component\HttpFoundation\Response;

abstract class TestCase extends Orchestra
{
    /** @var TestResponse<Response>|null */
    public static ?TestResponse $latestResponse = null;

    /**
     * @param class-string<FormRequest> $requestClass
     * @param array<string, string> $headers
     */
    protected function makeRequest(string $requestClass, array $headers = [], string $method = 'POST'): TestFormRequest
    {
        $requestFactory = new class($this->app, $this->makeUrl(...))
        {
            use ValidatesRequests;

            protected Application $app;

            /** @var array<string, string> */
            protected array $serverVariables;

            /** @var Closure(string): string */
            private readonly Closure $urlBuilder;

            public function __construct(Application $app, Closure $urlBuilder)
            {
                $this->app = $app;
                $this->serverVariables = [];
                $this->urlBuilder = $urlBuilder;
            }

            /**
             * @param class-string<FormRequest> $requestClass
             * @param array<string, string> $headers
             */
            public function make(string $requestClass, array $headers = [], string $method = 'POST'): TestFormRequest
            {
                return $this->createRequest($requestClass, $headers, $method);
            }

            protected function prepareUrlForRequest(string $uri): string
            {
                return ($this->urlBuilder)($uri);
            }

            /**
             * @return array<string, string>
             */
            protected function prepareCookiesForRequest(): array
            {
                return [];
            }

            /**
             * @param array<string, string> $headers
             * @return array<string, string>
             */
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
}
