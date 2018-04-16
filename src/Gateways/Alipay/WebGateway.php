<?php
namespace anerg\Payment\Gateways\Alipay;

use anerg\Payment\Connector\Datasheet;
use anerg\Payment\Gateways\Alipay\BizData;

class WebGateway extends _Gateway
{

    public function pay()
    {
        //设定交易接口名称
        Datasheet::set('method', 'alipay.trade.page.pay');
        //设定手机支付的特定参数
        BizData::set('product_code', 'FAST_INSTANT_TRADE_PAY');

        return $this->buildPayHtml();
    }
}
