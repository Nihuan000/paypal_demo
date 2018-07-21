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
            'total' => 0.01,
            'order_desc' => '金币充值支付凭证'
        ];
        vendor("Paypal.PaymentClient");
        $paypal = new \PaymentClient();
        $paymentProcess = $paypal->create_paypal_payment($order);
        if($paymentProcess['code'] == 1){
            $approvalUrl = $paymentProcess['approvalUrl'];
            echo json_encode([
                'status' => 1,
                'msg' => '付款地址获取成功',
                'result' => [
                    'approvalUrl' => $approvalUrl
                ]
            ]);
        }else{
            echo json_encode([
                'status' => 0,
                'msg' => '生成失败',
                'result' => []
            ]);
        }
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