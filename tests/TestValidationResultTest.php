<?php

namespace MarkWalet\TestableRequests\Tests;

use MarkWalet\TestableRequests\Tests\Fixtures\SampleFormRequest;

class TestValidationResultTest extends TestCase
{
    public function test_it_can_assert_a_specific_field_passes_while_others_fail(): void
    {
        $result = $this->makeRequest(SampleFormRequest::class)
            ->withParam('account', 'acme')
            ->validate([
                'title' => 'okay',
                'email' => 'not-an-email',
                'account' => 'acme',
            ]);

        $result->assertPassesValidationFor('title');
    }
}
