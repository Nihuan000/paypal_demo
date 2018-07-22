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
    /**
     * paypal 支付地址生成接口
     */
    public function pay_for_paypal()
    {
        //支付方式 1：paypal 2: credit_card
        $payment_method = I('payment_method',1);
        //货币类型
        $currency = I('currency','USD');
        //支付总额
        $total_amount = I('total_amount');
        //订单编号，根据项目的编号生成规则修改
        $order_num = uniqid();
        $order = [
            'method' => $payment_method == 2 ? 'credit_card' : 'paypal',
            'currency' => $currency,
            'total' => $total_amount,
            'order_desc' => '金币充值支付凭证',
            'order_num' => $order_num
        ];
        vendor("Paypal.PaymentClient");
        $paypal = new \PaymentClient();
        $paymentProcess = $paypal->create_paypal_payment($order);
        if($paymentProcess['code'] == 1){
            //返回授权登录网址给客户端，由客户端webview调用并引导用户登录付款
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


    /**
     * 用户登录并提交付款成功之后的回调地址
     */
    public function process_payment_success()
    {

        $paymentId = trim($_GET['paymentId']);
        $payerID = trim($_GET['PayerID']);
        if(!empty($paymentId) && !empty($payerID)){
            $paymentConf = [
                'paymentId' => $paymentId,
                'payerId' => $payerID
            ];
            vendor("Paypal.PaymentClient");
            $payment = new \PaymentClient();
            $paymentSucess = $payment->complate_paypal_payment($paymentConf);
            if($paymentSucess['code'] == 1){
                //TODO 确认支付后订单状态修改/金币充值操作
                echo "success";
                return $paymentSucess['payment'];
            }else{
                echo "false";
            }
        }
    }


    /**
     * 用户取消付款后回调地址
     */
    public function cancel_payment_approval()
    {
        //TODO 这里处理买家取消付款后订单的一些操作

        echo 'Cancelled';
    }
}