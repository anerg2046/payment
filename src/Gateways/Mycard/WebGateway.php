<?php
namespace anerg\Payment\Gateways\Mycard;

use anerg\Payment\Connector\Datasheet;
use anerg\Payment\Gateways\Mycard\Helper;

class WebGateway extends _Gateway
{

    public function pay()
    {
        //设定交易接口名称
        Datasheet::set('TradeType', 2);
        $ret             = $this->unifiedorder();
        $domain          = Datasheet::get("SandBoxMode") === "true" ? Helper::SANDBOX_DOMAIN : Helper::PRODUCT_DOMAIN;
        $ret['redirect'] = $domain . '?' . $ret['AuthCode'];
        return $ret;
    }
}
