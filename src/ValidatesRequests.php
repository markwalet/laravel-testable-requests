<?php

namespace MarkWalet\TestableRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

trait ValidatesRequests
{
    /**
     * @param string $requestClass
     * @param array $headers
     * @param string $method
     * @return TestFormRequest
     */
    protected function createRequest(string $requestClass, array $headers = [], string $method = 'POST'): TestFormRequest
    {
        $symfonyRequest = SymfonyRequest::create(
            $this->prepareUrlForRequest('/test/route'),
            $method,
            [],
            $this->prepareCookiesForRequest(),
            [],
            array_replace($this->serverVariables, $this->transformHeadersToServerVars($headers))
        );

        $formRequest = FormRequest::createFrom(
            Request::createFromBase($symfonyRequest),
            new $requestClass
        )->setContainer($this->app);

        $route = new Route('POST', '/test/route', fn() => null);
        $route->parameters = [];
        $formRequest->setRouteResolver(fn() => $route);

        return new TestFormRequest($formRequest);
    }
}