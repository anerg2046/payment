<?php
namespace anerg\Payment\Gateways\Alipay;

use anerg\Payment\Gateways\Alipay\BizData;
use anerg\Payment\Gateways\Alipay\Helper;

abstract class _Gateway
{

    /**
     * 构造函数，添加接口请求参数
     */
    public function __construct($params)
    {
        BizData::set($params);

        //金额统一使用【分】为单位
        if (BizData::get('fee')) {
            BizData::set('fee', bcdiv(BizData::get('fee'), 100, 2));
        }
    }

    protected function buildPayHtml()
    {
        $params = Helper::getRequestParams();
        $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='" . Helper::GATE_WAY . "' method='POST'>";
        foreach ($params as $key => $val) {
            $val = str_replace("'", '&apos;', $val);
            $sHtml .= "<input type='hidden' name='" . $key . "' value='" . $val . "'/>";
        }
        $sHtml .= "<input type='submit' value='ok' style='display:none;''></form>";
        $sHtml .= "<script>document.forms['alipaysubmit'].submit();</script>";
        return $sHtml;
    }
}
