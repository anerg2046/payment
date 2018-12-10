<?php
namespace anerg\Payment\Gateways;

use anerg\Payment\Connector\Datasheet;
use anerg\Payment\Connector\Gateway;
use anerg\Payment\Gateways\Wxpay\Helper;
use anerg\Payment\Helper\Str;
use anerg\Payment\Helper\Xml;

class Wxpay extends Gateway
{

    /**
     * 构造函数，添加配置参数
     */
    public function __construct(array $config)
    {
        //设置字段别名，用于统一配置文件和订单提交参数
        //其他特定参数请参照官方文档进行设定
        $alias = [
            'app_id'    => 'appid',
            'order_no'  => 'out_trade_no',
            'refund_no' => 'out_refund_no',
            'client_ip' => 'spbill_create_ip',
            'fee'       => 'total_fee',
        ];
        Datasheet::setAlias($alias);
        Datasheet::set($config);
        //设置一些默认值，如果在$params中也有，则会覆盖
        $_default = [
            'spbill_create_ip' => Str::getClientIP(),
            'nonce_str'        => Str::random(),
        ];
        Datasheet::set($_default);
    }

    /**
     * 查询订单
     *
     * @param array $order 订单内容
     * @return array
     */
    public function query(array $order)
    {
        Datasheet::set($order);
        $param_keys = ['appid', 'mch_id', 'out_trade_no', 'transaction_id', 'nonce_str'];
        return Helper::request(Helper::GATEWAY_ORDERQUERY, $param_keys);
    }

    /**
     * 关闭未支付订单
     *
     * @param array $order 订单内容
     * @return array
     */
    public function close(array $order)
    {
        Datasheet::set($order);
        $param_keys = ['appid', 'mch_id', 'out_trade_no', 'nonce_str'];
        return Helper::request(Helper::GATEWAY_CLOSEORDER, $param_keys);
    }

    /**
     * 取消未支付订单
     *
     * @param array $order 订单内容
     * @return array
     */
    // public function cancel($order)
    // {
    //     throw new \Exception('微信不支持取消订单，请使用关闭订单');
    // }

    /**
     * 对订单进行退款操作
     *
     * @param array $order 订单内容
     * @return array
     */
    public function refund(array $order)
    {
        Datasheet::set($order);
        $param_keys = ['appid', 'mch_id', 'out_trade_no', 'transaction_id', 'out_refund_no',
            'total_fee', 'refund_fee', 'nonce_str'];
        return Helper::request(Helper::GATEWAY_REFUNDORDER, $param_keys, Helper::SSL_VERIFY);
    }

    /**
     * 验证服务器异步请求数据合法性并返回
     *
     * @return array
     */
    public function verify()
    {
        $data = file_get_contents("php://input");
        $data = Xml::xmlToArr($data);
        if ($data['sign'] != Helper::signature($data, Datasheet::get('md5_key'))) {
            throw new \Exception('微信异步通信签名验证失败');
        }
        return $data;
    }

    /**
     * 微信企业付款到零钱
     *
     * @return array
     */
    public function transfer($options)
    {
        Datasheet::set($options);
        Datasheet::set('mch_appid', Datasheet::get('appid'));
        Datasheet::set('mchid', Datasheet::get('mch_id'));
        Datasheet::set('partner_trade_no', Datasheet::get('transfer_no'));
        Datasheet::set('re_user_name', Datasheet::get('real_name'));
        $param_keys = ['mch_appid', 'mchid', 'device_info', 'nonce_str', 'partner_trade_no', 'openid', 'check_name',
            're_user_name', 'amount', 'desc', 'spbill_create_ip'];
        return Helper::request(Helper::GATEWAY_TRANSFER, $param_keys, true, false);
    }

    /**
     * 当异步处理完成，返回成功信息
     */
    public function success()
    {
        $ret = ['return_code' => 'SUCCESS', 'return_msg' => 'OK'];
        return Xml::arrToXml($ret);
    }

}
