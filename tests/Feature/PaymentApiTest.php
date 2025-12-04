<?php

namespace Tests\Feature;

use Tests\TestCase;

class PaymentApiTest extends TestCase
{
    public function test_get_payment_methods_endpoint()
    {
        $response = $this->get('/api/payments/methods');
        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
        $this->assertIsArray($response->json('methods'));
    }
}