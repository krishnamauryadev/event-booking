<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\PaymentService;

class PaymentServiceTest extends TestCase
{
    public function test_payment_service_charges_successfully()
    {
        $paymentService = new PaymentService();
        $result = $paymentService->charge(500);

        $this->assertArrayHasKey('status', $result);
        $this->assertContains($result['status'], ['success','failed']);
    }
}
