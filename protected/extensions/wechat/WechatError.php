<?php
/**
 * @Description: 微信错误类
 * @Author: yuzhibin
 * @CreateTime: 16/3/11 下午3:19
 */
class WechatError
{
    public static $normal = array(
        'error' => array(
            'error_id' => 0,
        ),
    );

    public static $index_request_error = array(
        'error' => array(
            'error_id' => 101,
            'error_str' => '入口请求参数有误'
        ),
    );

    public static $request_error = array(
        'error' => array(
            'error_id' => 102,
            'error_str' => '请求参数有误'
        ),
    );

    public static $miss_access_token = array(
        'error' => array(
            'error_id' => 103,
            'error_str' => 'access_token不存在'
        ),
    );

    public static $miss_method = array(
        'error' => array(
            'error_id' => 104,
            'error_str' => '接口方法不存在'
        ),
    );

    public static $add_edit_error = array(
        'error' => array(
            'error_id' => 105,
            'error_str' => '添加或修改失败'
        ),
    );

    public static $delete_error = array(
        'error' => array(
            'error_id' => 106,
            'error_str' => '删除失败'
        ),
    );


}
