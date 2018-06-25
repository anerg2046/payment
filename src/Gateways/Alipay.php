<?php
namespace anerg\Payment\Gateways;

use anerg\Payment\Connector\Datasheet;
use anerg\Payment\Connector\Gateway;
use anerg\Payment\Gateways\Alipay\BizData;
use anerg\Payment\Gateways\Alipay\Helper;

class Alipay extends Gateway
{
    /**
     * 构造函数，添加配置参数
     */
    public function __construct(array $config)
    {
        //设置字段别名，用于统一配置文件和订单提交参数
        //其他特定参数请参照官方文档进行设定
        $alias = [
            'order_no'   => 'out_trade_no',
            'client_ip'  => 'spbill_create_ip',
            'fee'        => 'total_amount',
            'limit_pay'  => 'disable_pay_channels',
            'body'       => 'subject',
            'detail'     => 'body',
            'attach'     => 'passback_params',
            'refund_no'  => 'out_request_no',
            'refund_fee' => 'refund_amount',
        ];
        Datasheet::setAlias($alias);
        Datasheet::set($config);
        //设置一些默认值，如果在$params中也有，则会覆盖
        $_default = [
            'timestamp' => date('Y-m-d H:i:s'),
            'charset'   => 'utf-8',
            'version'   => '1.0',
            'sign_type' => 'RSA2',
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
        BizData::set($order);
        //设定接口名称
        Datasheet::set('method', 'alipay.trade.query');

        return Helper::request();
    }

    /**
     * 关闭未支付订单
     *
     * @param array $order 订单内容
     * @return array
     */
    public function close(array $order)
    {
        BizData::set($order);
        //设定接口名称
        Datasheet::set('method', 'alipay.trade.close');

        return Helper::request();
    }

    /**
     * 取消未支付订单
     *
     * @param array $order 订单内容
     * @return array
     */
    public function cancel(array $order)
    {
        BizData::set($order);
        //设定接口名称
        Datasheet::set('method', 'alipay.trade.cancel');

        return Helper::request();
    }

    /**
     * 对订单进行退款操作
     *
     * @param array $order 订单内容
     * @return array
     */
    public function refund(array $order)
    {
        BizData::set($order);
        //金额统一使用【分】为单位
        if (BizData::get('refund_fee')) {
            BizData::set('refund_fee', bcdiv(BizData::get('refund_fee'), 100, 2));
        }
        //设定接口名称
        Datasheet::set('method', 'alipay.trade.refund');

        return Helper::request();
    }

    /**
     * 验证服务器异步请求数据合法性并返回
     *
     * @return array
     */
    public function verify()
    {
        $data = $_POST;
        $sign = $data['sign'];
        unset($data['sign_type']);
        foreach ($data as &$value) {
            $value = mb_convert_encoding($value, 'utf-8', 'gbk');
        }
        if (Helper::verify($data, $sign) === false) {
            throw new \Exception('支付宝异步通知验签失败');
        }
        return $data;
    }

    /**
     * 当异步处理完成，返回成功信息
     */
    public function success()
    {
        return 'success';
    }

}
