<?php
/**
 * @Description: 微信工具类
 * @Author: yuzhibin
 * @CreateTime: 16/3/11 上午9:20
 */
class WechatTool
{
    /**
     * curl请求
     * @param $opt
     * @return mixed
     */
    public static function curlFunc($opt)
    {
        $ch = curl_init();

        if (isset($opt['header'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER  , $opt['header']);
        }

        curl_setopt($ch, CURLOPT_URL, $opt['url']);
        curl_setopt($ch, CURLOPT_HEADER, 0);           //启用时会将头文件的信息作为数据流输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   //将curl_exec()获取的信息以文件流的形式返回，而不是直接输出

        if (isset($opt['post_data'])) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $opt['post_data']);
        }

        if (isset($opt['timeout'])) {
            curl_setopt($ch, CURLOPT_TIMEOUT, (int)$opt['timeout']);
        } else {
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        }

        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }

    /**
     * get请求
     * @param $url
     * @param $data
     * @return mixed
     */
    public static function curlGet($url, $data)
    {
        $url .= '?' . http_build_query($data);

        $opt = array(
            'url' => $url,
        );

        return self::curlFunc($opt);
    }

    /**
     * 请求百度Api
     * @param $url
     * @param $data
     * @return mixed
     */
    public static function baiduApi($flag, $data)
    {
        $url = '';
        switch ($flag) {
            case '天气':
                $url = 'http://apis.baidu.com/heweather/weather/free';
                break;
            default :
                break;
        }

        $url .= '?' . http_build_query($data);

        $opt = array(
            'url' => $url,
            'header' => array(
                'apikey:' . Yii::app()->params['baidu_apikey'],
            ),
        );

        return self::curlFunc($opt);
    }

    /**
     * 数组转对象
     * @param $array
     * @return mixed
     */
    public static function array_to_object($array)
    {
        $json = json_encode($array);
        return json_decode($json);
    }

    /**
     * 对象转数组
     * @param $object
     * @return mixed
     */
    public static function object_to_array($object)
    {
        $json = json_encode($object);
        return json_decode($json, true);
    }

}
