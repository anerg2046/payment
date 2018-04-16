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

## 支持的支付方式
- [支付宝支付](#支付宝支付)
- 微信支付
- 盈华讯方短信支付

### 支付宝
- PC网站支付
- 手机网站支付
- 手机APP支付
- 扫码支付

```php
//支付宝配置文件
return [
    'app_id' => '*************',
    'pem_private' => '/pathto/private.pem', //用户私钥
    'pem_public' => '/pathto/public.pem', //支付宝公钥
];
```