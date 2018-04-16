<?php
namespace anerg\Payment\Gateways\Alipay;

use anerg\Payment\Connector\Datasheet;
use anerg\Payment\Gateways\Alipay\BizData;
use anerg\Payment\Gateways\Alipay\Helper;

class AppGateway extends _Gateway
{

    public function pay()
    {
        //设定交易接口名称
        Datasheet::set('method', 'alipay.trade.app.pay');
        //设定手机支付的特定参数
        BizData::set('product_code', 'QUICK_MSECURITY_PAY');
        $params = Helper::getRequestParams();
        return http_build_query($params);
    }
}
