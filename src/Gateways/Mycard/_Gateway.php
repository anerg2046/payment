<?php
namespace anerg\Payment\Gateways\Mycard;

use anerg\Payment\Connector\Datasheet;
use anerg\Payment\Gateways\Mycard\Helper;

abstract class _Gateway
{

    /**
     * 构造函数，添加接口请求参数
     */
    public function __construct($params)
    {
        Datasheet::set($params);

        //金额统一使用【分】为单位
        if (Datasheet::get('fee')) {
            Datasheet::set('fee', bcdiv(Datasheet::get('fee'), 100, 2));
        }
    }

    /**
     * MyCard统一下单
     */
    public function unifiedorder()
    {
        $param_keys = ['FacServiceId', 'FacTradeSeq', 'TradeType', 'ServerId', 'CustomerId',
            'PaymentType', 'ItemCode', 'ProductName', 'Amount', 'Currency', 'SandBoxMode',
            'FacReturnURL', 'FacReturnURL',
        ];
        return Helper::request(Helper::GATEWAY_UNIFIEDORDER, $param_keys, Helper::SIGN_AUTH);
    }
}
