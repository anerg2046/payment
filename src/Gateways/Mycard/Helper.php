<?php

namespace anerg\Payment\Gateways\Mycard;

use anerg\Payment\Connector\Datasheet;
use \GuzzleHttp\Client;

class Helper
{
    const SANDBOX_DOMAIN       = 'https://test.b2b.mycard520.com.tw/';
    const PRODUCT_DOMAIN       = 'https://b2b.mycard520.com.tw/';
    const SANDBOX_JUMP_DOMAIN  = 'https://test.mycard520.com.tw/';
    const PRODUCT_JUMP_DOMAIN  = 'https://WWW.mycard520.com.tw/';
    const GATEWAY_UNIFIEDORDER = 'MyBillingPay/v1.1/AuthGlobal';
    const GATEWAY_TRADEQUERY   = 'MyBillingPay/v1.1/TradeQuery';
    const GATEWAY_TRADECONFIRM = 'MyBillingPay/v1.1/PaymentConfirm';

    const SIGN_AUTH     = 1;
    const SIGN_CALLBACK = 2;
    /**
     * 执行签名操作
     */
    public static function signature($type = self::SIGN_AUTH)
    {
        $fieldKeys = [];
        switch ($type) {
            case self::SIGN_AUTH:
                $fieldKeys = ['FacServiceId', 'FacTradeSeq', 'TradeType', 'ServerId', 'CustomerId',
                    'PaymentType', 'ItemCode', 'ProductName', 'Amount', 'Currency', 'SandBoxMode', 'FacReturnURL', 'hash_key'];
                break;
            case self::SIGN_CALLBACK:
                $fieldKeys = ['ReturnCode', 'PayResult', 'FactradeSeq', 'PaymentType', 'Amount',
                    'Currency', 'MyCardTradeNo', 'MyCardType', 'PromoCode', 'hash_key'];
                break;
        }
        $preHashValue = '';
        foreach ($fieldKeys as $key) {
            $preHashValue .= Datasheet::get($key);
        }
        $preHashValue = urlencode($preHashValue);
        $preHashValue = preg_replace_callback('~%[0-9A-F]{2}~', function ($matches) {
            return strtolower($matches[0]);
        }, $preHashValue);

        return hash('sha256', $preHashValue);
    }

    /**
     * 请求获取交易授权码
     *
     * @param string $url
     * @param array $params_keys
     * @param bool $ssl
     */
    public static function request($api, $param_keys, $signType = false)
    {
        $params = array_filter(Datasheet::get($param_keys));

        if ($signType !== false) {
            $params['Hash'] = self::signature($signType);
        }

        $client = new Client();
        $option = ['form_params' => $params];

        if (Datasheet::get('SandBoxMode') === "true") {
            $url = self::SANDBOX_DOMAIN . $api;
        } else {
            $url = self::PRODUCT_DOMAIN . $api;
        }

        $response = $client->request('POST', $url, $option);

        if ($response->getStatusCode() != '200') {
            throw new \Exception('网络发生错误，请稍后再试。cURL返回码：' . $response->getReasonPhrase());
        }
        $json = $response->getBody()->getContents();

        return self::response(json_decode($json, true));
    }

    /**
     * 对响应数据进行处理
     */
    public static function response($data)
    {
        if ($data['ReturnCode'] != '1') {
            throw new \Exception('MyCard请求接口发生系统级错误:[' . $data['ReturnMsg'] . ']');

        }
        return $data;
    }
}
