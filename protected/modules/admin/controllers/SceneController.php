<?php
/**
 * @Description: 带参数二维码
 * @Author: yuzhibin
 * @CreateTime: 16/3/28 下午1:15
 */

class SceneController extends Controller
{
    /**
     * 带参数二维码场景统计
     * @return void
     */
    public function actionSceneTotal()
    {
        //$total_data = Yii::app()->db->createCommand()
        //    ->select('sct.scene_id,sc.scene_remark,count(*) total_num,sct.timeint')
        //    ->from('wx_scene_total sct')
        //    ->leftJoin('wx_scene sc', 'sct.scene_id=sc.scene_id')
        //    ->where('sct.event=:event', array(':event' => 'subscribe'))
        //    ->group("sct.scene_id,DATE_FORMAT(sct.timeint, '%Y-%m-%d')")
        //    ->queryAll();

        $total_data = array();

        $this->render('sceneTotal', array(
            'total_data' => $total_data,
        ));
    }

    /**
     * 管理场景
     * @return void
     */
    public function actionSceneManagement()
    {
        //$scene_data = Yii::app()->db->createCommand()
        //    ->select('*')
        //    ->from('wx_scene')
        //    ->order('timeint desc')
        //    ->queryAll();

        $scene_data = array();

        $scene_count = count($scene_data);

        $this->render('sceneManagement', array(
            'scene_data' => $scene_data,
            'scene_count' => $scene_count,
        ));
    }

    /**
     * 添加场景
     * @return void
     */
    public function actionSceneAdd()
    {
        $this->render('sceneAdd');
    }

    /**
     * 场景编辑
     * @return void
     */
    public function actionSceneEdit()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : 0;

        $scene_data = Yii::app()->db->createCommand()
            ->select('*')
            ->from('wx_scene')
            ->where('id=:id', array(':id' => $id))
            ->queryRow();

        $this->render('sceneEdit', array(
            'scene_data' => $scene_data,
        ));
    }

    /**
     * 场景删除
     * @return void
     */
    public function actionSceneDel()
    {
        if (!isset($_POST['id']) || !isset($_POST['scene_id'])) {
            $re = WechatError::$request_error;
            goto end;
        }

        $del = Yii::app()->db->createCommand()
            ->delete('wx_scene', 'id=:id', array(':id' => $_POST['id']));

        $del2 = Yii::app()->db->createCommand()
            ->delete('wx_scene_total', 'scene_id=:scene_id', array(':scene_id' => $_POST['scene_id']));

        if ($del || $del2) {
            $re = WechatError::$normal;
            $re['data'] = array();
        } else {
            $re = WechatError::$delete_error;
        }

        end:
        echo json_encode($re);
        exit;
    }

    /**
     * 添加|修改场景接口
     * @return void
     */
    public function actionSceneAddOrEdit()
    {
        if (!(isset($_POST['code_type']) && isset($_POST['scene_id']) && isset($_POST['scene_remark']))) {
            $re = WechatError::$request_error;
            goto end;
        }

        $result = $this->getQRcodeUrl($_POST['scene_id'], $_POST['code_type']);  //获取二维码链接

        if (isset($result['error']['error_id']) && $result['error']['error_id'] == 0) {
            $url = $result['data']['url'];

            $insert_num = 0;
            $update_num = 0;
            $set_data = array(
                'scene_id' => $_POST['scene_id'],
                'scene_remark' => $_POST['scene_remark'],
                'url' => $url,
                'type' => $_POST['code_type'],
                'timeint' => time(),
            );

            if (!isset($_POST['id'])) {
                $insert_num = Yii::app()->db->createCommand()->insert('wx_scene', $set_data);  //添加
            } else {
                $update_num = Yii::app()->db->createCommand()->update('wx_scene', $set_data, 'id=:id', array(':id'=>$_POST['id']));  //修改
            }


            if ($insert_num || $update_num) {
                $re = WechatError::$normal;
                $re['data'] = array();
            } else {
                $re = WechatError::$add_edit_error;
            }

        } else {
            $re = $result;
        }

        end:
        echo json_encode($re);
        exit;
    }

    /**
     * 获取二维码链接
     * @param $scene_id
     * @param $code_type
     * @return mixed
     */
    private function getQRcodeUrl($scene_id, $code_type)
    {
        $query = array(
            'scene_id' => $scene_id,
            'code_type' => $code_type,
        );

        $opt = array(
            'url' => Yii::app()->request->hostInfo . '/wechatApi0101/index',
            'post_data' => array(
                'method' => 'w002',
                'query' => json_encode($query),
            ),
        );

        $result = json_decode(WechatTool::curlFunc($opt), true);

        return $result;
    }

}
