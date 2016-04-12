<?php
/**
 * @Description: 场景值统计模型
 * @Author: yuzhibin
 * @CreateTime: 16/4/12 下午7:11
 */

class SceneTotal extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{scene_total}}';
    }

    public static function getTotalData()
    {
        //$total_data = Yii::app()->db->createCommand()
        //    ->select('sct.scene_id,sc.scene_remark,count(*) total_num,sct.timeint')
        //    ->from('wx_scene_total sct')
        //    ->leftJoin('wx_scene sc', 'sct.scene_id=sc.scene_id')
        //    ->where('sct.event=:event', array(':event' => 'subscribe'))
        //    ->group("sct.scene_id,DATE_FORMAT(sct.timeint, '%Y-%m-%d')")
        //    ->queryAll();

        $data = self::model()->findAll(array(
            'condition' => 'event=:event',
            'params' => array(':event' => 'subscribe'),
        ));

        $result = CJSON::encode($data);

        return $result;
    }


}