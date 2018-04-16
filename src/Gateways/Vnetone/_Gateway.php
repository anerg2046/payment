<?php
namespace anerg\Payment\Gateways\Vnetone;

use anerg\Payment\Connector\Datasheet;
use anerg\Payment\Gateways\Vnetone\Helper;

abstract class _Gateway
{

    /**
     * 构造函数，添加接口请求参数
     * 请求参数会覆盖配置参数
     */
    public function __construct($params)
    {
        Datasheet::set($params);

        //金额统一使用【分】为单位
        if (Datasheet::get('fee')) {
            Datasheet::set('fee', bcdiv(Datasheet::get('fee'), 100, 0));
        }
    }

    protected function buildPayHtml()
    {
        $params = Helper::getRequestParams();
        // print_r($params);die;
        $sHtml = "<form id='Vnetonesubmit' name='Vnetonesubmit' action='" . Helper::GATE_WAY . "' method='POST'>";
        foreach ($params as $key => $val) {
            $val = str_replace("'", '&apos;', $val);
            $sHtml .= "<input type='hidden' name='" . $key . "' value='" . $val . "'/>";
        }
        $sHtml .= "<input type='submit' value='ok' style='display:none;''></form>";
        $sHtml .= "<script>document.forms['Vnetonesubmit'].submit();</script>";
        return $sHtml;
    }

}
