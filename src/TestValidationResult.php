<?php

namespace MarkWalet\TestableRequests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Stringable;

use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertArrayNotHasKey;
use function PHPUnit\Framework\assertContains;
use function PHPUnit\Framework\assertTrue;

class TestValidationResult
{
    private Validator $validator;

    private null|ValidationException $failed;

    /**
     * TestValidationResult constructor.
     *
     * @param Validator $validator
     * @param ValidationException|null $failed
     */
    public function __construct(Validator $validator, null|ValidationException $failed = null)
    {
        $this->validator = $validator;
        $this->failed = $failed;
    }

    /**
     * Assert that the request passes validation.
     *
     * @return $this
     */
    public function assertPassesValidation(): static
    {
        assertTrue($this->validator->passes(),
            sprintf("Validation of the payload:\n%s\ndid not pass validation rules\n%s\n",
                json_encode($this->validator->getData(), JSON_PRETTY_PRINT),
                json_encode($this->getFailedRules(), JSON_PRETTY_PRINT)
            )
        );

        return $this;
    }

    /**
     * Assert that the request passes validation.
     *
     * @return $this
     */
    public function assertPassesValidationFor(string $field): static
    {
        assertTrue($this->validator->fails());
        $failedRules = $this->getFailedRules();
        $failedFields = array_keys($failedRules);

        assertArrayNotHasKey($field, $failedRules, "Failed asserting that there was no validation error for '$field'. Got: ".json_encode($failedFields));

        return $this;
    }

    /**
     * Assert that the request fails validation.
     *
     * @param array<string, mixed> $expectedFailedRules
     * @return $this
     */
    public function assertFailsValidation(array $expectedFailedRules = []): static
    {
        assertTrue($this->validator->fails());

        if (empty($expectedFailedRules)) {
            return $this;
        }

        $failedRules = $this->getFailedRules();
        $failedFields = array_keys($failedRules);
        $expectedFailedRules = Arr::dot($expectedFailedRules);

        foreach ($expectedFailedRules as $field => $rule) {
            assertArrayHasKey($field, $failedRules, "Failed asserting that there was a validation error for '$field'. Got: ".json_encode($failedFields));
            assertContains((string) $rule, $failedRules[$field], "Failed asserting that '$field' got a $rule validation error. Got: ".json_encode($failedRules[$field]));
        }

        return $this;
    }

    /**
     * Assert that the request fails validation.
     *
     * @param string $field
     * @param Stringable|string|null $rule
     * @return $this
     */
    public function assertFailsValidationFor(string $field, Stringable|string|null $rule = null): static
    {
        assertTrue($this->validator->fails());
        $failedRules = $this->getFailedRules();
        $failedFields = array_keys($failedRules);

        assertArrayHasKey($field, $failedRules, "Failed asserting that there was a validation error for '$field'. Got: ".json_encode($failedFields));
        if ($rule !== null) {
            assertContains((string) $rule, $failedRules[$field], "Failed asserting that '$field' got a $rule validation error. Got: ".json_encode($failedRules[$field]));
        }

        return $this;
    }

    /**
     * Assert that the request contains a validation message.
     *
     * @param string $message
     * @param string|null $rule
     */
    public function assertHasMessage(string $message, null|string $rule = null): static
    {
        $validationMessages = $this->getValidationMessages($rule);
        assertContains($message, $validationMessages,
            sprintf("\"%s\" was not contained in the failed%s validation messages\n%s",
                $message, $rule ? ' '.$rule : '', json_encode($validationMessages, JSON_PRETTY_PRINT)
            )
        );

        return $this;
    }

    /**
     * Get a list of all rules that failed.
     *
     * @return array<string, list<string>>
     */
    public function getFailedRules(): array
    {
        if (! $this->failed) {
            return [];
        }

        $failedRules = [];

        foreach ($this->validator->failed() as $field => $details) {
            $fieldRules = [];

            foreach ($details as $rule => $constraints) {
                $implementedInterfaces = class_exists($rule) ? class_implements($rule) ?: [] : [];

                // TODO: Separate logic out to own class.
                $isCustomRule = array_intersect([ValidationRule::class, Rule::class], $implementedInterfaces) !== [];
                $normalizedRule = $isCustomRule
                    ? $rule
                    : Str::snake($rule);

                $fieldRules[] = count($constraints) > 0
                    ? $normalizedRule.':'.implode(',', $constraints)
                    : $normalizedRule;
            }

            $failedRules[$field] = $fieldRules;
        }

        return $failedRules;
    }

    /**
     * Get a list of validation messages from the request.
     *
     * @param string|null $rule
     * @return array<int, string>
     */
    private function getValidationMessages(null|string $rule = null): array
    {
        $messages = $this->validator->messages()->getMessages();
        if ($rule) {
            return $messages[$rule] ?? [];
        }

        return Arr::flatten($messages);
    }
}
