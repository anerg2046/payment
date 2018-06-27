<?php
namespace anerg\Payment\Connector;

use anerg\Payment\Connector\GatewayInterface;
use anerg\Payment\Helper\Str;

abstract class Gateway implements GatewayInterface
{
    /**
     * 执行支付操作
     *
     * @param string $method 支付方式
     * @param array $params 支付参数
     *
     * @return mixed string|array
     */
    public function pay($method, array $params)
    {
        $method = get_class($this) . '\\' . Str::uFirst($method) . 'Gateway';
        if (!class_exists($method)) {
            throw new \Exception('支付方式类[' . $method . ']不存在');
        }
        $app = new $method($params);
        return $app->pay();
    }

    /**
     * 查询订单
     *
     * @param array $order 订单内容
     * @return array
     */
    public function query(array $order)
    {
        $this->unsupport(__FUNCTION__);
    }

    /**
     * 关闭未支付订单
     *
     * @param array $order 订单内容
     * @return array
     */
    public function close(array $order)
    {
        $this->unsupport(__FUNCTION__);
    }

    /**
     * 取消未支付订单
     *
     * @param array $order 订单内容
     * @return array
     */
    public function cancel(array $order)
    {
        $this->unsupport(__FUNCTION__);
    }

    /**
     * 对订单进行退款操作
     *
     * @param array $order 订单内容
     * @return array
     */
    public function refund(array $order)
    {
        $this->unsupport(__FUNCTION__);
    }

    /**
     * 验证服务器异步请求数据合法性并返回
     *
     * @return array
     */
    public function verify()
    {
        $this->unsupport(__FUNCTION__);
    }

    /**
     * 当异步处理完成，返回成功信息
     */
    public function success()
    {
        $this->unsupport(__FUNCTION__);
    }

    /**
     * 魔术方法，执行支付操作
     *
     */
    public function __call($method, array $params)
    {
        return self::pay($method, ...$params);
    }

    /**
     * 不支持的方法异常抛出
     */
    private function unsupport($method)
    {
        $gateway = str_replace('anerg\\Payment\\Gateways\\', '', get_class($this));
        $msg     = '支付渠道[' . $gateway . ']不支持操作方式[' . $method . ']';
        throw new \Exception($msg);
    }
}
