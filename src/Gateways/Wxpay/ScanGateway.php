<?php
namespace anerg\Payment\Gateways\Wxpay;

use anerg\Payment\Connector\Datasheet;
use anerg\Payment\Gateways\Wxpay\Helper;
use anerg\Payment\Helper\Str;

class ScanGateway extends _Gateway
{

    public function pay()
    {
		//设定交易方式，并执行统一下单
        Datasheet::set('trade_type', 'NATIVE');
		$ret = $this->unifiedorder();
		return $ret['code_url'];
    }
}
