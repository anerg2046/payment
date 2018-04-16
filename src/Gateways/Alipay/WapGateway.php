<?php
namespace anerg\Payment\Gateways\Alipay;

use anerg\Payment\Connector\Datasheet;
use anerg\Payment\Gateways\Alipay\BizData;

class WapGateway extends _Gateway
{

    public function pay()
    {
        //设定交易接口名称
        Datasheet::set('method', 'alipay.trade.wap.pay');
        //设定手机H5支付的特定参数
        BizData::set('product_code', 'QUICK_WAP_WAY');

        return $this->buildPayHtml();
    }
}
