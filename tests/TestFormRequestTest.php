<?php

namespace MarkWalet\TestableRequests\Tests;

use Illuminate\Auth\GenericUser;
use MarkWalet\TestableRequests\Tests\Fixtures\SampleFormRequest;

class TestFormRequestTest extends TestCase
{
    public function test_it_validates_with_default_data_and_route_parameters(): void
    {
        $result = $this->makeRequest(SampleFormRequest::class)
            ->withParam('account', 'acme')
            ->defaultData([
                'title' => 'Valid title',
                'email' => 'mark@example.com',
            ])
            ->validate([
                'account' => 'acme',
            ]);

        $result->assertPassesValidation();
    }

    public function test_it_reports_validation_failures_and_messages(): void
    {
        $result = $this->makeRequest(SampleFormRequest::class)
            ->withParam('account', 'acme')
            ->validate([
                'email' => 'not-an-email',
                'account' => 'other',
            ]);

        $result
            ->assertFailsValidation([
                'title' => 'required',
                'email' => 'email',
                'account' => 'in:acme',
            ])
            ->assertFailsValidationFor('title', 'required')
            ->assertHasMessage('A title is required.', 'title');
    }

    public function test_it_asserts_authorization_for_a_user(): void
    {
        $request = $this->makeRequest(SampleFormRequest::class)
            ->by(new GenericUser(['can_submit' => true]));

        $request->assertAuthorized();
    }

    public function test_assert_not_authorized_alias_uses_unauthorized_assertion(): void
    {
        $request = $this->makeRequest(SampleFormRequest::class)
            ->by(new GenericUser(['can_submit' => false]));

        $request->assertNotAuthorized();
    }
}
