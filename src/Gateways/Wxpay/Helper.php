<?php
namespace anerg\Payment\Gateways\Wxpay;

use anerg\Payment\Connector\Datasheet;
use anerg\Payment\Helper\Str;
use anerg\Payment\Helper\Xml;
use \GuzzleHttp\Client;

class Helper
{
    const GATEWAY_UNIFIEDORDER = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
    const GATEWAY_ORDERQUERY   = 'https://api.mch.weixin.qq.com/pay/orderquery';
    const GATEWAY_CLOSEORDER   = 'https://api.mch.weixin.qq.com/pay/closeorder';
    const GATEWAY_REFUNDORDER  = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
    const GATEWAY_SHORT_URL    = 'https://api.mch.weixin.qq.com/tools/shorturl';

    const SSL_VERIFY = true;

    /**
     * 执行签名操作
     */
    public static function signature($data = [], $key = null)
    {
        if (is_null($key)) {
            throw new \Exception('缺少微信签名密钥[md5_key]');
        }
        ksort($data);
        $str = Str::buildParams($data) . '&key=' . $key;
        return strtoupper(md5($str));
    }

    /**
     * 对微信响应数据进行处理
     */
    public static function response($data, $key = null)
    {
        if ($data['return_code'] != 'SUCCESS') {
            throw new \Exception('微信请求接口发生系统级错误:[' . $data['return_msg'] . (isset($data['err_code_des']) ? $data['err_code_des'] : '') . ']');

        }
        if ($data['sign'] != self::signature($data, $key)) {
            throw new \Exception('微信返回签名验证失败');
        }
        return $data;
    }

    /**
     * 对微信服务端发起请求
     *
     * @param string $url
     * @param array $params_keys
     * @param bool $ssl
     */
    public static function request($url, $param_keys, $ssl = false)
    {
        $params         = array_filter(Datasheet::get($param_keys));
        $params['sign'] = self::signature($params, Datasheet::get('md5_key'));

        $client = new Client();
        $option = ['body' => Xml::arrToXml($params)];

        if ($ssl === true) {
            $option['cert']    = Datasheet::get('pem_cert');
            $option['ssl_key'] = Datasheet::get('pem_key');
        }

        $response = $client->request('POST', $url, $option);

        if ($response->getStatusCode() != '200') {
            throw new \Exception('网络发生错误，请稍后再试。cURL返回码：' . $response->getReasonPhrase());
        }
        $xml = $response->getBody()->getContents();

        return self::response(Xml::xmlToArr($xml), Datasheet::get('md5_key'));
    }
}
