<?php

namespace App\Services;

class PaymentService
{
    public function charge($amount)
    {
        $cents = intval(round($amount * 100));
        if ($cents % 2 === 0) return ['status'=>'success','transaction_id'=>uniqid('txn_')];
        return ['status'=>'failed','transaction_id'=>uniqid('txn_')];
    }
}
