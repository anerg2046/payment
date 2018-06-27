<?php
namespace anerg\Payment\Gateways\Wxpay;

use anerg\Payment\Connector\Datasheet;
use anerg\Payment\Gateways\Wxpay\Helper;
use anerg\Payment\Helper\Str;

class AppGateway extends _Gateway
{

    public function pay()
    {
        //设定交易方式，并执行统一下单
        Datasheet::set('trade_type', 'APP');
        $ret = $this->unifiedorder();
        //拼接APP支付的返回参数
        $appParams = [
            'appid'     => Datasheet::get('app_id'),
            'partnerid' => Datasheet::get('mch_id'),
            'prepayid'  => $ret['prepay_id'],
            'package'   => 'Sign=WXPay',
            'noncestr'  => Str::random(),
            'timestamp' => time() . '',
        ];
        $appParams['sign'] = Helper::signature($appParams, Datasheet::get('md5_key'));
        return $appParams;
    }
}
