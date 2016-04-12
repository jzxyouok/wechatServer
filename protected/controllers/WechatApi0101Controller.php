<?php
/**
 * @Description: 微信接口类
 * @Author: yuzhibin
 * @CreateTime: 16/3/22 下午5:56
 */

//w系列 --> 微信官方接口
//s系列 --> 带参数二维码接口

class WechatApi0101Controller extends Controller
{
    /**
     * 接口入口
     * @return void
     */
    public function actionIndex()
    {
        if (!isset($_POST['method']) || !isset($_POST['query'])) {
            $result = WechatError::$index_request_error;
            goto end;
        }

        $_POST['query'] = json_decode($_POST['query'], true);

        $method = $_POST['method'];

        if (method_exists($this, $method)) {
            $result = $this->$method();

        } else {
            $result = WechatError::$miss_method;
        }

        end:
        echo json_encode($result);
        exit;
    }

    /**
     * 获取access_token
     * @return array|mixed
     */
    private function w001()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/token';
        $get_data = array(
            'grant_type' => 'client_credential',
            'appid' => Yii::app()->params['wechat_appID'],
            'secret' => Yii::app()->params['wechat_appsecret'],
        );

        $result = json_decode(WechatTool::curlGet($url, $get_data), true);
        $redis_cli = Yii::app()->redis->getClient();

        if (isset($result['access_token'])) {
            $redis_cli->setex('wechat_access_token', 7000, $result['access_token']);        //将access_token写入redis, 生命周期为差3分钟到就2小时

            $re = WechatError::$normal;
            $re['data'] = $result;
        } else {
            $redis_cli->del('wechat_access_token');
            $re = $result;
        }

        return $re;
    }

    /**
     * 验证access_token是否存在
     * @return mixed
     */
    private function accessTokenExist()
    {
        $redis_cli = Yii::app()->redis->getClient();
        $access_token_exist = $redis_cli->exists('wechat_access_token');

        if (!$access_token_exist) {
            $this->w001();
        }

        $access_token = $redis_cli->get('wechat_access_token');

        return $access_token;
    }

    /**
     * 获取带参数二维码的Ticket和url
     * @return mixed
     */
    private function w002()
    {
        $query = $_POST['query'];

        if (!(isset($query['scene_id']) && isset($query['code_type']))) {
            $re = WechatError::$request_error;
            goto end;
        }

        $access_token = $this->accessTokenExist();

        if ($access_token) {
            $post_data = array(
                'action_info' => array(
                    'scene' => array(
                        'scene_id' => $query['scene_id'],
                    )
                ),
            );

            //临时或永久二维码
            if ($query['code_type'] == 0) {
                $post_data['action_name'] = 'QR_SCENE';
                $post_data['expire_seconds'] = 2592000;
            } else {
                $post_data['action_name'] = 'QR_LIMIT_SCENE';
            }

            $opt = array(
                'url' => 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $access_token,
                'post_data' => json_encode($post_data),
            );

            $result = json_decode(WechatTool::curlFunc($opt), true);
            if (isset($result['ticket'])) {
                $re = WechatError::$normal;
                $re['data'] = $result;
            } else {
                $re = $result;
            }

        } else {
            $re = WechatError::$miss_access_token;
        }

        end:
        return $re;
    }

    /**
     * 获取带参数的二维码图片
     * @return void
     */
    private function w003()
    {
        $ticket = 'gQE08DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL21FaC10UnJtdVpUQXV3ZHNhbUI3AAIEzuvwVgMEAAAAAA==';

        $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode';
        $get_data = array(
            'ticket' => urlencode($ticket),
        );

        $result = json_decode(WechatTool::curlGet($url, $get_data));

        var_dump($result);
    }

    /**
     * 自定义菜单
     * @return array|mixed
     */
    private function w004()
    {
        $query = $_POST['query'];
        if (!isset($query['button_menu'])) {
            $re = WechatError::$request_error;
            goto end;
        }

        $access_token = $this->accessTokenExist();

        if ($access_token) {
            $post_data = $query['button_menu'];

            $opt = array(
                'url' => 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . $access_token,
                'post_data' => json_encode($post_data, JSON_UNESCAPED_UNICODE),   //不对中文unicode编码
            );

            $result = json_decode(WechatTool::curlFunc($opt), true);

            if ($result['errcode'] == 0) {
                $re = WechatError::$normal;
                $re['data'] = $result;
            } else {
                $re = $result;
            }

        } else {
            $re = WechatError::$miss_access_token;
        }

        end:
        return $re;
    }

    private function s001()
    {
        $result = WechatError::$normal;
        $result['data'] = json_decode(SceneTotal::getTotalData(), true);

        return $result;
    }

}
