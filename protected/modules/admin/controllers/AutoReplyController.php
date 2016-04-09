<?php
/**
 * @Description: 自动回复
 * @Author: yuzhibin
 * @CreateTime: 16/3/28 上午11:36
 */

class AutoReplyController extends Controller
{
    /**
     * 被添加自动回复（关注）
     * @return void
     */
    public function actionConcern()
    {
        $this->render('concern');
    }

    /**
     * 关键字自动回复
     * @return void
     */
    public function actionKeyword()
    {
        $this->render('keyword');
    }

}
