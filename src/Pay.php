<?php
namespace anerg\Payment;

use anerg\Payment\Connector\GatewayInterface;
use anerg\Payment\Helper\Str;

class Pay
{
    protected static function init($gateway, $config)
    {
        $gateway = Str::uFirst($gateway);
        $class   = __NAMESPACE__ . '\\Gateways\\' . $gateway;
        if (class_exists($class)) {
            $app = new $class($config);
            if ($app instanceof GatewayInterface) {
                return $app;
            }
            throw new \Exception("支付基类 [$gateway] 必须继承抽象类 [GatewayInterface]");
        }
        throw new \Exception("支付基类 [$gateway] 不存在");
    }

    public static function __callStatic($gateway, $config)
    {
        return self::init($gateway, ...$config);
    }
}
