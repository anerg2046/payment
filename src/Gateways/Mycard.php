<?php

namespace anerg\Payment\Gateways;

use anerg\Payment\Connector\Datasheet;
use anerg\Payment\Connector\Gateway;
use anerg\Payment\Gateways\Mycard\Helper;

/**
 * 台湾MyCard支付
 */
class Mycard extends Gateway
{
    /**
     * 构造函数，添加配置参数
     */
    public function __construct(array $config)
    {
        //设置字段别名，用于统一配置文件和订单提交参数
        //其他特定参数请参照官方文档进行设定
        $alias = [
            'app_id'   => 'FacServiceId',
            'order_no' => 'FacTradeSeq',
            'user_id'  => 'CustomerId',
            'body'     => 'ProductName',
            'fee'      => 'Amount',
        ];
        Datasheet::setAlias($alias);
        Datasheet::set($config);
    }

    /**
     * 处理MyCard通知
     *
     * @return array
     */
    public function callback($data = null)
    {
        $data = $data ?: $_POST;
        if ($data['ReturnCode'] != 1) {
            throw new \Exception("交易查询失败：" . $data['ReturnMsg']);
        }
        Datasheet::set($data);
        if (isset($data['Hash']) && $data['Hash'] != Helper::signature(Helper::SIGN_CALLBACK)) {
            throw new \Exception("回调签名验证失败");
        }

        if ($data['PayResult'] != 3) {
            throw new \Exception("交易不成功：" . $data['ReturnMsg']);
        }
        return $data;
    }

    /**
     * 查询交易
     *
     * @param array $order
     * @return array
     */
    public function query(array $order)
    {
        Datasheet::set($order);
        $result = Helper::request(Helper::GATEWAY_TRADEQUERY, ['AuthCode']);
        return $this->callback($result);
    }

    /**
     * 确认交易-执行划款
     *
     * @param array $order
     * @return array
     */
    public function confirm(array $order)
    {
        Datasheet::set($order);
        $result = Helper::request(Helper::GATEWAY_TRADECONFIRM, ['AuthCode']);
        if ($result['ReturnCode'] != 1) {
            throw new \Exception("交易确认失败：" . $data['ReturnMsg']);
        }
        return $result;
    }
}
