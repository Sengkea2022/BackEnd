<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiTestRouteTest extends TestCase
{
    public function test_api_test_route_returns_a_successful_json_response(): void
    {
        $response = $this->getJson('/api/test');

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Backend API is connected.',
                'status' => 'ok',
            ]);
    }
}
