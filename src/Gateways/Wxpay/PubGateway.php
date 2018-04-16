<?php
namespace anerg\Payment\Gateways\Wxpay;

use anerg\Payment\Connector\Datasheet;
use anerg\Payment\Gateways\Wxpay\Helper;
use anerg\Payment\Helper\Str;

class PubGateway extends _Gateway
{

    public function pay()
    {
		//设定交易方式，并执行统一下单
        Datasheet::set('trade_type', 'JSAPI');
		$ret = $this->unifiedorder();
		//拼接公众号支付的javascript参数
        $jsParams = [
            'appId' => Datasheet::get('app_id'),
            'timeStamp' => time() . '',
            'nonceStr' => Str::random(),
            'package' => 'prepay_id=' . $ret['prepay_id'],
            'signType' => 'MD5',
        ];
		$jsParams['sign'] = Helper::signature($jsParams, Datasheet::get('md5_key'));
		return $jsParams;
    }
}
