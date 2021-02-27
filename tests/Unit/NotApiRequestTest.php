<?php

namespace Tests\Unit;

use Tests\TestCase;

class NotApiRequestTest extends TestCase
{
    /**
     * Any direct HTTP request (not /api) should return 404.
     *
     * @return void
     */
    public function test_not_api_requests_returns_404()
    {
        $response = $this->get('*');

        $response->assertStatus(404);
    }
}
