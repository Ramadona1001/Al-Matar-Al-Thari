<?php

namespace Tests\Feature;

use Tests\TestCase;

class AnalyticsEndpointsTest extends TestCase
{
    public function test_metrics_endpoint_returns_success()
    {
        $this->withoutMiddleware();
        $response = $this->get('/api/analytics/metrics');
        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
    }

    public function test_transactions_endpoint_returns_success()
    {
        $this->withoutMiddleware();
        $response = $this->get('/api/analytics/transactions');
        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
    }
}