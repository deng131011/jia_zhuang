<?php
// 上传

namespace app\home\controller;

class Upload extends Home {

    function _initialize()
    {
        parent::_initialize();
        if (!$this->currentUser || !$this->currentUser['uid']) {
           // $this->redirect(url('home/login/index'));
        }
    }

    /**
     * 文件上传
     * @return [type] [description]
     * @date   2018-02-16
     * @author 心云间、凝听 <981248356@qq.com>
     */
    public function upload() {

        $return = logic('common/Upload')->upload();
        return json($return);
    }
    
    /**
     * 附件信息
     * @param  integer $id [description]
     * @return [type] [description]
     * @date   2018-08-12
     * @author 心云间、凝听 <981248356@qq.com>
     */
    public function attachmentInfo($id=0)
    {
        try {
            if ($id>0) {
                $info = get_attachment_info($id);//附件信息
                $this->assign('info',$info);

                //获取分类数据
                $media_cats = model('Terms')->getList(['taxonomy'=>'media_cat']);
                $this->assign('media_cats',$media_cats);
                return $this->fetch();
            } else{
                throw new \Exception("参数不合法", 0);
                
            }
            
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}