<?php
namespace anerg\Payment\Gateways;

use anerg\Payment\Connector\Datasheet;
use anerg\Payment\Connector\Gateway;
use anerg\Payment\Gateways\Vnetone\Helper;

/**
 * 盈华讯方短信支付
 */
class Vnetone extends Gateway
{
    /**
     * 构造函数，添加配置参数
     */
    public function __construct(array $config)
    {
        //设置字段别名，用于统一配置文件和订单提交参数
        //其他特定参数请参照官方文档进行设定
        $alias = [
            'app_id'     => 'sp',
            'app_secret' => 'sppwd',
            'order_no'   => 'od',
            'fee'        => 'mz',
            'attach'     => 'spzdy',
            'quit_url'   => 'spreq',
            'return_url' => 'spsuc',
        ];
        Datasheet::setAlias($alias);
        Datasheet::set($config);
    }

    /**
     * 验证服务器异步请求数据合法性并返回
     *
     * @return array
     */
    public function verify()
    {
        $data = $_GET;
        $sign = $data['md5'];

        if (Helper::verify($data, $sign) === false) {
            throw new \Exception('盈华讯方异步通知验签失败');
        }
        return $data;
    }

    /**
     * 当异步处理完成，返回成功信息
     */
    public function success()
    {
        return 'okydzf';
    }

}
