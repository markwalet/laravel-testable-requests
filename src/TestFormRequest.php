<?php

namespace MarkWalet\TestableRequests;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
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
     * @return TestValidationResult
     */
    public function validate(array $data = []): TestValidationResult
    {
        $data = array_merge($this->defaultData, $data);
        $this->request->request = new InputBag($data);

        /** @var Validator $validator */
        $validator = Closure::fromCallable(function () {
            return $this->getValidatorInstance();
        })->call($this->request);

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
     * @return $this
     */
    public function by(Authenticatable $user = null)
    {
        $this->request->setUserResolver(fn() => $user);

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
        foreach($params as $param => $value) {
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
            $this->bully(fn() => $this->passesAuthorization(), $this->request),
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
        $this->assertNotAuthorized();
    }

    /**
     * Assert that the request is unauthorized.
     *
     * @return void
     */
    public function assertUnauthorized(): void
    {
        assertFalse(
            $this->bully(fn() => $this->passesAuthorization(), $this->request),
            'The provided user is authorized by this request'
        );
    }

    private function bully(Closure $elevatedFunction, object $targetObject)
    {
        return Closure::fromCallable($elevatedFunction)->call($targetObject);
    }
}
