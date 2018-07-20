## Paypal 支付说明

标签（空格分隔）： Paypal PHP 接口

---

> 接口文件放在ThinkPHP/Library/Vendor/下

### 目录文件说明
文件|说明
---|:--:
PaypalConfig.php|接口配置
PaymentClient.php|接口调用方法
vendor|paypal API

#### 返回值
* -1 请求参数不完整
* 0 paypal接口请求失败
* 1 接口请求成功

#### 调用方法
- [x]  get_access_token 获取入口令牌
- [x] create_paypal_payment 创建付款
- [x] filter_params 请求参数过滤
- [x] payment_log 支付日志方法
- [x] returnMsg 格式化输出调用结果





