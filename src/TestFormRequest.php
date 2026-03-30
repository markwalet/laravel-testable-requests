<?php

namespace MarkWalet\TestableRequests;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\InputBag;

use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

class TestFormRequest
{
    private FormRequest $request;

    /** @var array<string, mixed> */
    private array $defaultData = [];

    /**
     * TestFormRequest constructor.
     *
     * @param FormRequest $request
     */
    public function __construct(FormRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Set a default payload for the request.
     * This payload will get merged with the payload given in the `validate()` method.
     *
     * @param array<string, mixed> $data
     *
     * @return $this
     */
    public function defaultData(array $data = []): static
    {
        $this->defaultData = $data;

        return $this;
    }

    /**
     * Validate the request with the given data.
     *
     * @param array<string, mixed> $data
     */
    public function validate(array $data = []): TestValidationResult
    {
        $data = array_merge($this->defaultData, $data);
        $this->request->request = new InputBag($data);

        $reflectionMethod = new ReflectionMethod($this->request, 'getValidatorInstance');
        $reflectionMethod->setAccessible(true);

        /** @var Validator $validator */
        $validator = $reflectionMethod->invoke($this->request);

        try {
            $validator->validate();
        } catch (ValidationException $e) {
            return new TestValidationResult($validator, $e);
        }

        return new TestValidationResult($validator);
    }

    /**
     * Execute the request for a given user.
     *
     * @param Authenticatable|null $user
     */
    public function by(null|Authenticatable $user = null): static
    {
        $this->request->setUserResolver(fn () => $user);

        return $this;
    }

    /**
     * Add route parameters to the request.
     *
     * @param array<string, mixed> $params
     * @return $this
     */
    public function withParams(array $params): static
    {
        foreach ($params as $param => $value) {
            $this->withParam($param, $value);
        }

        return $this;
    }

    /**
     * Add a route parameter to the request.
     *
     * @param string $param
     * @param mixed $value
     * @return $this
     */
    public function withParam(string $param, mixed $value): static
    {
        $this->request->route()->setParameter($param, $value);

        return $this;
    }

    /**
     * Assert that the request is authorized.
     *
     * @return void
     */
    public function assertAuthorized(): void
    {
        assertTrue(
            $this->passesAuthorization(),
            'The provided user is not authorized by this request'
        );
    }

    /**
     * Assert that the request is unauthorized.
     *
     * @return void
     */
    public function assertNotAuthorized(): void
    {
        $this->assertUnauthorized();
    }

    /**
     * Assert that the request is unauthorized.
     *
     * @return void
     */
    public function assertUnauthorized(): void
    {
        assertFalse(
            $this->passesAuthorization(),
            'The provided user is authorized by this request'
        );
    }

    private function passesAuthorization(): bool
    {
        $reflectionMethod = new ReflectionMethod($this->request, 'passesAuthorization');
        $reflectionMethod->setAccessible(true);

        /** @var bool $authorized */
        $authorized = $reflectionMethod->invoke($this->request);

        return $authorized;
    }
}
