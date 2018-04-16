<?php
namespace anerg\Payment\Connector;

interface GatewayInterface
{
    /**
     * 执行支付操作
     *
     * @param string $method 支付方式
     * @param array $params 支付参数
     *
     * @return mixed string|array
     */
    public function pay(string $method, array $params);

    /**
     * 查询订单
     *
     * @param array $order 订单内容
     * @return array
     */
    public function query(array $order);

    /**
     * 关闭未支付订单
     *
     * @param array $order 订单内容
     * @return array
     */
    public function close(array $order);

    /**
     * 取消未支付订单
     *
     * @param array $order 订单内容
     * @return array
     */
    public function cancel(array $order);

    /**
     * 对订单进行退款操作
     *
     * @param array $order 订单内容
     * @return array
     */
    public function refund(array $order);

    /**
     * 验证服务器异步请求数据合法性并返回
     *
     * @return array
     */
    public function verify();

    /**
     * 当异步处理完成，返回成功信息
     */
    public function success();
}
