<?php
/**
 * Created by PhpStorm.
 * User: nihuan
 * Date: 18-7-20
 * Time: 下午12:02
 * Desc: Paypal 支付接口
 */
require __DIR__ . '/vendor/autoload.php';
require dirname(__FILE__) . '/PaypalConfig.php';

use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
class PaymentClient
{

    private $appKey;
    private $appSecret;
    private $paypal_log;
    private $log_date;

    public function __construct()
    {
        $this->appKey = PaypalConfig::PAYPAL_APP_ID;
        $this->appSecret = PaypalConfig::PAYPAL_APP_SCERET;
        $this->paypal_log = RUNTIME_PATH . 'paypal_log/' . date('Y_m_d') . '.log';
        $this->log_date = date('Y-m-d H:i:s');
    }


    /**
     * 授权令牌获取
     * @return ApiContext
     */
    protected function get_access_token()
    {
        $apiContent = new ApiContext(
            new OAuthTokenCredential(
                $this->appKey,
                $this->appSecret
            )
        );

        $apiContent->setConfig(
            [
                'model' => PaypalConfig::PAYPAL_MODE,
                'log.LogEnabled' => PaypalConfig::PAYPAL_LOG_ENABLE,
                'log.FileName' => $this->paypal_log,
                'log.LogLevel' => PaypalConfig::PAYPAL_LOG_LEVEL,
                'cache.enabled' => true,
            ]
        );
        return $apiContent;
    }


    /**
     * Paypal创建付款
     * @param array $params
     * @return array
     */
    public function create_paypal_payment($params = [])
    {
        $this->filter_params(['method','currency','total'], $params);
        $apiContext = $this->get_access_token();
        //创建付款进程
        $payer = new Payer();
        $payer->setPaymentMethod($params['method']);

        //设置回调地址
        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(PaypalConfig::PAYPAL_CALLBACK_URL);
        $redirectUrls->setCancelUrl(PaypalConfig::PAYPL_CANCEL_URL);

        //设置付款金额及货币类型
        $amount = new Amount();
        $amount->setCurrency($params['currency']);
        $amount->setTotal($params['total']);

        //设置事务对象
        $transaction = new Transaction();
        $transaction->setAmount($amount);
        $transaction->setDescription($params['order_desc']);

        //付款请求对象汇总
        $payment = new Payment();
        $payment->setIntent('sale');
        $payment->setPayer($payer);
        $payment->setRedirectUrls($redirectUrls);
        $payment->setTransactions($transaction);

        try {
            $payment->create($apiContext);
            $approvalUrl = $payment->getApprovalLink();
            var_dump($approvalUrl);exit;
        } catch (PayPal\Exception\PayPalConnectionException $ex) {
            $log_content = "responseCode: {$ex->getCode()}, responseData: {$ex->getData()}, responseMsg: {$ex->getMessage()}";
            $this->payment_log($log_content,2);
            $this->returnMsg(0,$ex->getMessage(),[$ex->getData()]);
        } catch (Exception $ex)
        {
            $log_content = "responseCode: {$ex->getCode()}, responseMsg: {$ex->getMessage()}";
            $this->payment_log($log_content,2);
            $this->returnMsg(0,$ex->getMessage(),[]);
        }
    }


    /**
     * 参数过滤
     * @param array $params
     * @return bool
     */
    protected function filter_params($filter = [], $params = [])
    {
        $filter = array_flip($filter);
        if(isset($filter['method']) && empty($params['method'])){
            $this->returnMsg(-1,'必须指定 method');
        }elseif(isset($filter['currency']) && empty($params['currency'])){
            $this->returnMsg(-1,'必须指定 currency');
        }elseif(isset($filter['total']) && empty($params['total'])){
            $this->returnMsg(-1,'必须指定 total');
        }else{
            return true;
        }
    }

    /**
     * 支付日志记录
     * @param $content
     * @param int $type
     */
    protected function payment_log($content,$type = 0)
    {
        switch($type){
            case 0:
                //TODO 创建成功日志
                $msg = '创建付款成功';
                break;

            case 1:
                //TODO 取消创建日志
                $msg = '创建付款取消';
                break;

            case 2:
                //TODO 创建失败日志
                $msg = '创建付款失败';
                break;
        }

        $log_content = "[{$this->log_date}] {$msg} {$content}" . PHP_EOL;
        file_put_contents($this->paypal_log,$log_content,FILE_APPEND);
    }


    /**
     * 接口请求结果格式化
     * @param int $code
     * @param string $msg
     * @param array $data
     * @return array
     */
    protected function returnMsg($code = 1, $msg = '', $data = [])
    {
        return [
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ];
    }
}