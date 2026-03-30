<?php

namespace MarkWalet\TestableRequests;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

trait ValidatesRequests
{
    /**
     * @param class-string<FormRequest> $requestClass
     * @param array<string, string> $headers
     */
    protected function createRequest(string $requestClass, array $headers = [], string $method = 'POST'): TestFormRequest
    {
        /** @var Application $app */
        $app = $this->app;

        /** @var array<string, string> $serverVariables */
        $serverVariables = $this->serverVariables;

        $symfonyRequest = SymfonyRequest::create(
            $this->prepareUrlForRequest('/test/route'),
            $method,
            [],
            $this->prepareCookiesForRequest(),
            [],
            array_replace($serverVariables, $this->transformHeadersToServerVars($headers))
        );

        $formRequest = FormRequest::createFrom(
            Request::createFromBase($symfonyRequest),
            new $requestClass
        )->setContainer($app);

        $route = new Route('POST', '/test/route', fn () => null);
        $route->parameters = [];
        $formRequest->setRouteResolver(fn () => $route);

        return new TestFormRequest($formRequest);
    }
}
