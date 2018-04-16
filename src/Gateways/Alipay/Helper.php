<?php
namespace anerg\Payment\Gateways\Alipay;

use anerg\Payment\Connector\Datasheet;
use anerg\Payment\Helper\Str;
use \GuzzleHttp\Client;

class Helper
{
    const GATE_WAY = 'https://openapi.alipay.com/gateway.do';

    const RSA_PRIVATE = 1;
    const RSA_PUBLIC = 2;

    /**
     * 获取公共请求参数
     */
    public static function baseParams()
    {
        $param_keys = ['app_id', 'timestamp', 'charset', 'version', 'sign_type', 'method'];
        return Datasheet::get($param_keys);
    }

    /**
     * 获取本次要使用的请求参数
     */
    public static function getRequestParams()
    {
        $params = self::baseParams();
        $params['biz_content'] = json_encode(BizData::all());
        $params['sign'] = self::signature($params);
        return $params;
    }

    public static function signature($data = [])
    {
        ksort($data);
        $str = Str::buildParams($data);

        $rsaKey = self::getRsaKeyVal(self::RSA_PRIVATE);
        $res = openssl_get_privatekey($rsaKey);
        $sign = '';
        openssl_sign($str, $sign, $res, OPENSSL_ALGO_SHA256);
        openssl_free_key($res);
        return base64_encode($sign);
    }

    public static function verify($data, $sign = null)
    {
        if (is_array($data)) {
            $data = mb_convert_encoding(json_encode($data, JSON_UNESCAPED_UNICODE), 'gbk', 'utf-8');
        }
        $rsaKey = self::getRsaKeyVal(self::RSA_PUBLIC);
        $res = openssl_get_publickey($rsaKey);
        $result = (bool) openssl_verify($data, base64_decode($sign), $res, OPENSSL_ALGO_SHA256);
        openssl_free_key($res);
        return $result;
    }

    protected static function getRsaKeyVal($type = self::RSA_PUBLIC)
    {
        if ($type === self::RSA_PUBLIC) {
            $keyname = 'pem_public';
            $header = '-----BEGIN PUBLIC KEY-----';
            $footer = '-----END PUBLIC KEY-----';
        } else {
            $keyname = 'pem_private';
            $header = '-----BEGIN RSA PRIVATE KEY-----';
            $footer = '-----END RSA PRIVATE KEY-----';
        }
        $rsa = Datasheet::get($keyname);
        if (is_file($rsa)) {
            $rsa = file_get_contents($rsa, 'r');
        }
        if (empty($rsa)) {
            throw new \Exception('支付宝RSA密钥未配置');
        }
        $rsa = str_replace([PHP_EOL, $header, $footer], '', $rsa);
        $rsaVal = $header . PHP_EOL . chunk_split($rsa, 64, PHP_EOL) . $footer;
        return $rsaVal;
    }

    public static function request()
    {
        $params = self::getRequestParams();
        $client = new Client();
        $option = ['form_params' => $params];

        $response = $client->request('POST', self::GATE_WAY, $option);

        if ($response->getStatusCode() != '200') {
            throw new \Exception('网络发生错误，请稍后再试。cURL返回码：' . $response->getReasonPhrase());
        }
        $ret = $response->getBody()->getContents();

        //支付宝返回数据如果有中文内容，则是gbk编码的，需要转换
        $ret = mb_convert_encoding($ret, 'utf-8', 'gbk');
        return self::response(json_decode($ret, true));
    }

    public static function response($data)
    {
        // print_r($data);
        $method = str_replace('.', '_', Datasheet::get('method')) . '_response';
        if (self::verify($data[$method], $data['sign']) === false) {
            throw new \Exception('支付宝返回数据验签失败');
        }
        if (isset($data[$method]['code']) && $data[$method]['code'] == '10000') {
            return $data[$method];
        }
        throw new \Exception('支付宝接口错误:' . $data[$method]['msg'] . ' ' . ($data[$method]['sub_code'] ?? '') . ' ' . ($data[$method]['sub_msg'] ?? ''), $data[$method]['code']);
    }
}
