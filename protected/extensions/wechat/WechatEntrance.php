<?php
/**
 * @Description: 微信入口
 * @Author: yuzhibin
 * @CreateTime: 16/2/24 下午1:41
 */

class WechatEntrance
{
    /**
     * 验证服务器地址有效性
     * @return void
     */
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        //valid signature , option
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = Yii::app()->params['wechat_token'];
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 消息响应
     * @return void
     */
    public function responseMsg()
    {
        $post_str = $GLOBALS["HTTP_RAW_POST_DATA"];

        if (!empty($post_str)){
            $post_obj = simplexml_load_string($post_str, 'SimpleXMLElement', LIBXML_NOCDATA);
            $msg_type = $post_obj->MsgType;

            $result = '';
            switch ($msg_type) {
                case 'text':      //文本
                    $result = $this->receiveText($post_obj);
                    break;
                case 'event':     //事件推送
                    $result = $this->receiveEvent($post_obj);
                    break;
                default :
                    break;
            }

            echo $result;
        }else {
            echo "";
        }

        exit;
    }

    /**
     * 接受文本消息
     * @param $post_obj
     * @return string
     */
    private function receiveText($post_obj)
    {
        $content = trim($post_obj->Content);

        $exist = Yii::app()->db->createCommand()
            ->select('*')
            ->from('wx_keyword')
            ->where("keyword like '%$content%' and type=0")
            ->queryRow();

        $result = '';
        if ($exist) {
            if (!empty($exist['text'])) {
                //回复文本消息
                $result = $this->transmitText($post_obj, $exist['text']);
            } else {
                //回复多图文或当图文消息
                $title_arr = explode('-->', $exist['title']);
                $description_arr = explode('-->', $exist['description']);
                $picurl_arr = explode('-->', $exist['picurl']);
                $url_arr = explode('-->', $exist['url']);

                $news_info = array();
                for ($i = 0; $i < count($title_arr); $i++) {
                    $item_arr = array(
                        'title' => $title_arr[$i],
                        'description' => $description_arr[$i],
                        'picurl' => $picurl_arr[$i],
                        'url' => $url_arr[$i],
                    );

                    array_push($news_info, $item_arr);
                }

                $result = $this->transmitNews($post_obj, $news_info);
            }
        }

        return $result;
    }

    /**
     * 回复文本消息
     * @param $post_obj
     * @param $content
     * @return string
     */
    private function transmitText($post_obj, $content)
    {
        if (!isset($content) || empty($content)) {
            return '';
        }

        $tpl = "<xml>
                 <ToUserName><![CDATA[%s]]></ToUserName>
                 <FromUserName><![CDATA[%s]]></FromUserName>
                 <CreateTime>%s</CreateTime>
                 <MsgType><![CDATA[%s]]></MsgType>
                 <Content><![CDATA[%s]]></Content>
                 <FuncFlag>0</FuncFlag>
                 </xml>";

        $result = sprintf($tpl, $post_obj->FromUserName, $post_obj->ToUserName, time(), 'text', $content);

        return $result;
    }

    /**
     * 回复图文消息
     * @param $post_obj
     * @param $news_info
     * @return string
     */
    private function transmitNews($post_obj, $news_info)
    {
        $item_tpl = "<item>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                        <PicUrl><![CDATA[%s]]></PicUrl>
                        <Url><![CDATA[%s]]></Url>
                    </item>";
        $item_tpl_result = '';

        foreach ($news_info as $k => $v) {
            $item_tpl_result .= sprintf($item_tpl, $v['title'], $v['description'], $v['picurl'], $v['url']);
        }

        $tpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <ArticleCount>%s</ArticleCount>
                    <Articles>

                    " . $item_tpl_result ."

                    </Articles>
                </xml>";

        $result = sprintf($tpl, $post_obj->FromUserName, $post_obj->ToUserName, time(), 'news', count($news_info));

        return $result;
    }

    /**
     * 接受事件推送
     * @param $post_obj
     * @return string
     */
    private function receiveEvent($post_obj)
    {
        $event = $post_obj->Event;

        $result = '';
        switch ($event) {
            case 'subscribe':    //关注
                $result = $this->dealSubscribe($post_obj);
                break;
            case 'unsubscribe':  //取消关注
                break;
            case 'SCAN':         //扫描，已关注
                $result = $this->dealSCAN($post_obj);
                break;
            case 'CLICK':        //点击菜单拉取消息
                $result = $this->dealClick($post_obj);
                break;
            default :
                break;
        }

        return $result;
    }

    /**
     * 处理关注事件
     * @param $post_obj
     * @return string
     */
    private function dealSubscribe($post_obj)
    {
        $exist = Yii::app()->db->createCommand()
            ->select('*')
            ->from('wx_keyword')
            ->where("type=1")
            ->queryRow();

        $result = '';

        if ($exist) {
            $content = $exist['text'];
            $result = $this->transmitText($post_obj, $content);
        }

        $event = $post_obj->Event;
        $event_key = isset($post_obj->EventKey) ? $post_obj->EventKey : '';
        $ticket = isset($post_obj->Ticket) ? $post_obj->Ticket : '';

        $this->saveScene($event, $event_key, $ticket);

        return $result;
    }

    /**
     * 处理扫描事件
     * @param $post_obj
     * @return string
     */
    private function dealSCAN($post_obj)
    {
        $event = $post_obj->Event;
        $event_key = isset($post_obj->EventKey) ? $post_obj->EventKey : '';
        $ticket = isset($post_obj->Ticket) ? $post_obj->Ticket : '';

        $this->saveScene($event, $event_key, $ticket);

        return '';
    }

    /**
     * 保存场景值
     * @param $event
     * @param $event_key
     * @param $ticket
     * @return void
     */
    private function saveScene($event, $event_key, $ticket)
    {
        if (!empty($event_key)) {
            $scene_id = str_replace('qrscene_', '', $event_key);

            Yii::app()->db->createCommand()->insert('wx_scene_total', array(
                'scene_id' => $scene_id,
                'timeint' => time(),
                'event' => $event,
                'ticket' => $ticket,
            ));
        }
    }

    /**
     * 处理点击菜单拉取信息事件
     * @param $post_obj
     * @return string
     */
    private function dealClick($post_obj)
    {
        $event_key = $post_obj->EventKey;

        $result = '';
        switch ($event_key) {
            case 'PLAY_001':
                $news_info = array(
                    array(
                        'title' => '妙计旅行飞跃2015的12种姿势 | 你好2016',
                        'description' => '你好2016！',
                        'picurl' => 'http://mmbiz.qpic.cn/mmbiz/9MvlvXLibCZ7eJS3a63WoRia90fvjSanYhjs7cqwy39ZOlG76lkYfzHibpr6yGlZH6vichYL9E3Gqg55bBhmSY6Lxw/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1',
                        'url' => 'http://mp.weixin.qq.com/s?__biz=MzA5NTQyMzAxOQ==&mid=401213323&idx=1&sn=d9c33079eeb42b35bb6119bbb198548c&scene=18#rd'
                    ),
                );

                $result = $this->transmitNews($post_obj, $news_info);
                break;
            case 'PLAY_002':
                $news_info = array(
                    array(
                        'title' => '活动总结 | “旅行自变量”线下沙龙圆满收官',
                        'description' => '从“极客玩+”到“小资本论”，从“慵玩主义”到“科技旅人”，“旅行自变量”纵穿整个中国将聪明旅行的理念进行到底。',
                        'picurl' => 'http://mmbiz.qpic.cn/mmbiz/9MvlvXLibCZ4UpqvXzSm4gZKLD7J9zMAf3s0Yn8OwHjLQdfREA7OqJw1XxJ06k8ricUCIb6aTrZNT6goMUT9vobA/640?wx_fmt=jpeg&tp=webp&wxfrom=5',
                        'url' => 'http://mp.weixin.qq.com/s?__biz=MzA5NTQyMzAxOQ==&mid=208794901&idx=1&sn=f2c2556d6f0e45a8c04853ca30197ed2&scene=18#rd'
                    ),
                    array(
                        'title' => '视频 | Miogeek北京·极客玩+',
                        'description' => '旅行自变量·北京站极客玩+本文为妙计旅行原创内容转载请注明版权来源及妙计旅行二维码感谢你对原创的支持。',
                        'picurl' => 'http://mmbiz.qpic.cn/mmbiz/9MvlvXLibCZ4UpqvXzSm4gZKLD7J9zMAf2efyS8YTl03GBDibmcSXxP55s7CdwmKXf3D4NU46Y5v6p2aTv1icRpMQ/640?wx_fmt=jpeg&tp=webp&wxfrom=5',
                        'url' => 'http://mp.weixin.qq.com/s?__biz=MzA5NTQyMzAxOQ==&mid=208794901&idx=2&sn=80ac6db06f8c222eae1296a091b31761&scene=18#rd'
                    ),
                    array(
                        'title' => '视频 | Miogeek上海·小资本论',
                        'description' => '旅行自变量·上海站小资本论本文为妙计旅行原创内容转载请注明版权来源及妙计旅行二维码感谢你对原创的支持。',
                        'picurl' => 'http://mmbiz.qpic.cn/mmbiz/9MvlvXLibCZ4UpqvXzSm4gZKLD7J9zMAfFduWCJ2mFAeKfDAmKicTfEuOKbyfjmg32kSRhRY4AtzIjuF9PwOu3kw/640?wx_fmt=jpeg&tp=webp&wxfrom=5',
                        'url' => 'http://mp.weixin.qq.com/s?__biz=MzA5NTQyMzAxOQ==&mid=208794901&idx=3&sn=998ac987f4b36a8b4c48a399f3a04c6a&scene=18#rd'
                    ),
                    array(
                        'title' => '视频 | Miogeek成都·慵玩主义',
                        'description' => '旅行自变量·成都站慵玩主义本文为妙计旅行原创内容转载请注明版权来源及妙计旅行二维码感谢你对原创的支持。',
                        'picurl' => 'http://mmbiz.qpic.cn/mmbiz/9MvlvXLibCZ4UpqvXzSm4gZKLD7J9zMAfFRxaO7icLDu41c5FeqSurEVibAOHDrGSTaWrE3AMNiaM2jV7gVKnIR56A/640?wx_fmt=jpeg&tp=webp&wxfrom=5',
                        'url' => 'http://mp.weixin.qq.com/s?__biz=MzA5NTQyMzAxOQ==&mid=208794901&idx=4&sn=16901a7415b62e2fcd130cc4997f0514&scene=18#rd'
                    ),
                );

                $result = $this->transmitNews($post_obj, $news_info);
                break;
            case 'JOIN_001':
                $news_info = array(
                    array(
                        'title' => '这儿，有一个根治不爱上班的偏方。',
                        'description' => '你有病吗？我这里有药……',
                        'picurl' => 'http://mmbiz.qpic.cn/mmbiz/9MvlvXLibCZ71gtFCbGQiaWwupyqSgSdFrzwgWSO4aibAwRHTf2pMibdbv7WtB5mjXTlgG4icD7Nw8VlreJzW50xv5Q/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1',
                        'url' => 'http://mp.weixin.qq.com/s?__biz=MzA5NTQyMzAxOQ==&mid=208132181&idx=1&sn=14e4b90b0323b8d294794a57dd625bd3&scene=18#rd'
                    ),
                );

                $result = $this->transmitNews($post_obj, $news_info);
                break;
            default :
                break;
        }

        return $result;
    }

}
