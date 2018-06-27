<?php
namespace anerg\Payment\Gateways\Wxpay;

use anerg\Payment\Connector\Datasheet;

class WapGateway extends _Gateway
{

    public function pay()
    {
        //设定交易方式，并执行统一下单
        Datasheet::set('trade_type', 'MWEB');
        $ret    = $this->unifiedorder();
        $schema = $ret['mweb_url'];
        if (Datasheet::get('redirect_url')) {
            $schema .= '&redirect_url=' . Datasheet::get('redirect_url');
        }
        return $schema;
    }
}
