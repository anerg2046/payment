# 支付类
===============

## 安装方法
```
composer require anerg2046/payment
```

>类库使用的命名空间为`\\anerg\\Payment`

## 运行环境
- PHP 5.6+
- composer

## 支持的支付渠道
- [支付宝支付/Alipay](#支付宝支付方式)
- [微信支付/Wxpay](#微信支付方式)
- [盈华讯方短信支付/Vnetone](#盈华讯方支付方式)

## 统一订单信息
```php
$params = [
    'order_no' => '*************', //商户订单号，无论什么渠道
    'fee' => 100, //支付金额，统一使用【分】为单位，无论什么支付渠道
    'body' => '商品名称', //无论什么渠道
    'detail' => '商品描述', //无论什么渠道
    'client_ip' => '127.0.0.1', //客户端IP
    'limit_pay' => '*****', //禁用的支付方式
    'attach' => '附加参数', //用户自定义值，会在回调或回跳的时候原样返回的值
    'refund_no' => '****************', //退款单号
    'refund_fee' => 100, //退款金额
];
```
* 注意，这些参数在所有渠道中的官方文档定义可能不一样，在这里我做了统一处理
* 当你在订单信息要传递参数的时候可以选择使用以上的参数名，当然也可以使用官方的参数名
* 未出现在上面的参数名，你需要使用官方的要求的参数名

## 典型用法
```php
try {
	$ret = Pay::支付渠道名称($配置文件信息)->支付方式名称($统一订单信息);
	var_dump($ret);
}catch(\Exception $e) {
	echo $e->getMessage();
}
```

## 支持的支付方式

### 支付宝支付方式
- PC网站支付/Web
- 手机网站支付/Wap
- 手机APP支付/App
- 扫码支付/Scan

```php
//支付宝配置文件
return [
    'app_id' => '*************',
    'pem_private' => '/pathto/private.pem', //用户私钥
    'pem_public' => '/pathto/public.pem', //支付宝公钥
];
>支付方式名称
```
|  method   |   描述
| :-------: | :-------:
|  Web      | 电脑支付
|  Wap      | 手机网站支付
|  App      | APP 支付
|  Scan     | 扫码支付

### 微信支付方式
- PC网站支付/Web
- 手机网站支付/Wap
- 手机APP支付/App
- 扫码支付/Scan

```php
//微信配置文件
return [
    'app_id' => '***********',
    'app_secret' => '*******************',
    'mch_id' => '****************',
    'md5_key' => '*******************',
    'notify_url' => 'http://test.com/wxpay/notify',
    'pem_cert' => '/pathto/apiclient_cert.pem',
    'pem_key' => '/pathto/apiclient_key.pem',
];
>支付方式名称
```
|  method   |   描述
| :-------: | :-------:
|  Pub      | 公众号支付
|  App      | APP 支付
|  Scan     | 扫码支付