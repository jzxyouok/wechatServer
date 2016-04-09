<?php
/**
 * @Description: 微信控制器
 * @Author: yuzhibin
 * @CreateTime: 16/2/24 下午1:34
 */

class WechatController extends Controller
{
    /**
     * 访问微信入口
     * @return void
     */
    public function actionIndex()
    {
        $wechat_obj = new WechatEntrance();
        if (isset($_GET['echostr'])) {
            $wechat_obj->valid();
        } else {
            $wechat_obj->responseMsg();
        }
    }

    public function actionTest()
    {
        $button_arr = array(
            'button' => array(
                array(
                    'type' => 'view',
                    'name' => '翻旧帐',
                    'url' => 'http://mp.weixin.qq.com/mp/getmasssendmsg?__biz=MzA5NTQyMzAxOQ==#wechat_webview_type=1&wechat_redirect'
                ),
                array(
                    'name' => '跟我玩',
                    'sub_button' => array(
                        array(
                            'type' => 'click',
                            'name' => '妙计2015',
                            'key' => 'PLAY_001',
                        ),
                        array(
                            'type' => 'click',
                            'name' => '妙计自变量',
                            'key' => 'PLAY_002',
                        ),
                    ),
                ),
                array(
                    'name' => '求勾搭',
                    'sub_button' => array(
                        array(
                            'type' => 'click',
                            'name' => '加入我们',
                            'key' => 'JOIN_001',
                        )
                    ),
                )
            ),
        );

        print_var(json_encode($button_arr, JSON_UNESCAPED_UNICODE));
    }

}
