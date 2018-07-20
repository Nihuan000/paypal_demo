<?php
/**
 * Created by PhpStorm.
 * User: nihuan
 * Date: 18-7-20
 * Time: 下午12:01
 */

class PaypalConfig
{
    //paypal key
    const PAYPAL_APP_ID = '';

    //paypal sceret
    const PAYPAL_APP_SCERET = '';

    //paypal 付款确认回调地址
    const PAYPAL_CALLBACK_URL = 'http://localhost/php_demo/payDemo/index.php/Home/Paypal/process_payment_success';

    //paypal 付款取消地址
    const PAYPL_CANCEL_URL = 'http://localhost/php_demo/payDemo/index.php/Home/Paypal/cancel_payment_approval';

    //paypal运行模式 测试用sandbox, 线上环境用live
    const PAYPAL_MODE = 'sandbox';

    //paypal日志开关
    const PAYPAL_LOG_ENABLE = true;

    // paypal 日志等级,线上环境用 INFO
    const PAYPAL_LOG_LEVEL = 'ALL';

}