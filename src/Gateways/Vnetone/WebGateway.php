<?php
namespace anerg\Payment\Gateways\Vnetone;

class WebGateway extends _Gateway
{

    public function pay()
    {
        return $this->buildPayHtml();
    }
}
