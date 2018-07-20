<?php
/**
 * Created by PhpStorm.
 * User: nihuan
 * Date: 18-7-20
 * Time: 下午2:51
 */

namespace Home\Controller;

class PaypalController
{
    public function pay_for_paypal()
    {
        $order = [
            'method' => 'paypal',
            'currency' => 'USD',
            'total' => 20,
            'order_desc' => 'paypal支付订单测试'
        ];
        vendor("Paypal.PaymentClient");
        $paypal = new \PaymentClient();
        $paymentProcess = $paypal->create_paypal_payment($order);
        exit;
    }

    public function process_payment_success()
    {
        echo json_encode('success');
    }

    public function cancel_payment_approval()
    {
        echo json_encode('Cancelled');
    }
}