<?php
namespace anerg\Payment\Gateways\Wxpay;

use anerg\Payment\Connector\Datasheet;
use anerg\Payment\Gateways\Wxpay\Helper;

abstract class _Gateway
{

    /**
     * 构造函数，添加接口请求参数
     * 请求参数会覆盖配置参数
     */
    public function __construct($params)
    {
        Datasheet::set($params);
    }

    /**
     * 微信统一下单
     */
    public function unifiedorder()
    {
        $param_keys = ['appid', 'mch_id', 'body', 'out_trade_no', 'total_fee', 'nonce_str', 'spbill_create_ip', 'notify_url',
            'device_info', 'sign_type', 'detail', 'attach', 'fee_type', 'time_start', 'time_expire',
            'goods_tag', 'trade_type', 'product_id', 'limit_pay', 'openid', 'scene_info',
        ];
        return Helper::request(Helper::GATEWAY_UNIFIEDORDER, $param_keys);
    }
}
