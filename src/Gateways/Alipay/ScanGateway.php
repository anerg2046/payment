<?php
namespace anerg\Payment\Gateways\Alipay;

use anerg\Payment\Connector\Datasheet;
use anerg\Payment\Gateways\Alipay\Helper;

class ScanGateway extends _Gateway
{

    public function pay()
    {
        //设定交易接口名称
        Datasheet::set('method', 'alipay.trade.precreate');

        return Helper::request();
    }
}
