<?php
namespace anerg\Payment\Gateways\Vnetone;

use anerg\Payment\Connector\Datasheet;

class Helper
{
    const GATE_WAY = 'http://ydzf.vnetone.com/mobilepayment/orderPayment.aspx';

    /**
     * 获取本次要使用的请求参数
     */
    public static function getRequestParams()
    {
        $params        = Datasheet::get(['sp', 'od', 'sppwd', 'mz', 'spreq', 'spsuc', 'uid', 'attach']);
        $params['md5'] = self::signature($params);
        $params        = array_filter($params);
        return $params;
    }

    public static function signature($data = [])
    {
        $str = $data['sp'] . $data['od'] . Datasheet::get('sppwd') . $data['mz'] . $data['spreq'] . $data['spsuc'];

        return strtoupper(md5($str));
    }

    public static function verify($data, $sign = null)
    {
        $str = $data['oid'] . $data['sporder'] . $data['spid'] . $data['mz'] . Datasheet::get('sppwd');
        return strtoupper(md5($str)) == $sign;
    }
}
