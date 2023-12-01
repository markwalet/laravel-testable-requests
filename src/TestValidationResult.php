<?php

namespace MarkWalet\TestableRequests;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Stringable;
use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertContains;
use function PHPUnit\Framework\assertStringContainsString;
use function PHPUnit\Framework\assertTrue;

class TestValidationResult
{
    private Validator $validator;
    private ?ValidationException $failed;

    /**
     * TestValidationResult constructor.
     *
     * @param Validator $validator
     * @param ValidationException|null $failed
     */
    public function __construct(Validator $validator, ?ValidationException $failed = null)
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

        $failedRules = Arr::dot($this->getFailedRules());
        $expectedFailedRules = Arr::dot($expectedFailedRules);

        foreach ($expectedFailedRules as $expectedFailedRule => $constraints) {
            assertArrayHasKey($expectedFailedRule, $failedRules);
            assertStringContainsString($constraints, $failedRules[$expectedFailedRule]);
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
        $failedRules = Arr::dot($this->getFailedRules());
        $failedFields = array_keys($failedRules);

        assertArrayHasKey($field, $failedRules, "Failed asserting that there was a validation error for '$field'. Got: ". json_encode($failedFields));

        if ($rule !== null) {
            assertStringContainsString($rule, $failedRules[$field]);
        }

        return $this;
    }

    /**
     * Assert that the request contains a validation message.
     *
     * @param $message
     * @param $rule
     * @return $this
     */
    public function assertHasMessage($message, $rule = null): static
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
     * @return array
     */
    public function getFailedRules(): array
    {
        if (!$this->failed) {
            return [];
        }

        return collect($this->validator->failed())
            ->map(function ($details) {
                return collect($details)->reduce(function ($aggregateRule, $constraints, $ruleName) {
                    $failedRule = class_exists($ruleName) ? $ruleName : Str::lower($ruleName);

                    if (count($constraints)) {
                        $failedRule .= ':'.implode(',', $constraints);
                    }

                    return $aggregateRule.$failedRule;
                });
            })
            ->toArray();
    }

    /**
     * Get a list of validation messages from the request.
     *
     * @param $rule
     * @return array<int, string>
     */
    private function getValidationMessages($rule = null): array
    {
        $messages = $this->validator->messages()->getMessages();
        if ($rule) {
            return $messages[$rule] ?? [];
        }

        return Arr::flatten($messages);
    }
}