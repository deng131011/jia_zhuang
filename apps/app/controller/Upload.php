<?php
// 上传

namespace app\app\controller;
use app\common\logic\Upload as UploadLogic;
class Upload extends Home {


    /**
     * 文件上传
     * @return [type] [description]
     */
    public function upload() {
        $list = file_get_contents('php://input');
        $post = json_decode($list, true);

       // $this->appReturn(array('code'=>205,'fileinfo'=>$_FILES));

        /*$param = [];
        if($post['file_type']==1){
            $param['type'] = 'image'; //图片
        }else if($post['file_type']==2){
            $param['type'] = 'file'; //文件
        }*/



        $controller = new UploadLogic;
       // $post['type'] = $post['type']=='file' ? 'file' : 'image';
        $return = $controller->upload();
        //$this->appReturn(array('code'=>205,'data'=>$return));
        if($return['code']==1){
            $return['code'] = 200;
        }

        $this->appReturn($return);
    }
	
	 /**
     * 文件上传
     * @return [type] [description]
     */
    public function uploadone() {
        $list = file_get_contents('php://input');
        $post = json_decode($list, true);

      //  $this->appReturn($_FILES);exit;

        /*$param = [];
        if($post['file_type']==1){
            $param['type'] = 'image'; //图片
        }else if($post['file_type']==2){
            $param['type'] = 'file'; //文件
        }*/

        $param['type'] = 'image'; //图片
        $return = logic('common/Upload')->upload($param);
        //$this->appReturn(array('code'=>205,'data'=>$return));
        if($return['code']==1){
            $return['code'] = 200;
        }

        $this->appReturn($return);
    }
	
	public function upload_file1() {
        $list = file_get_contents('php://input');
        $post = json_decode($list, true);

       // $this->appReturn(array('code'=>205,'fileinfo'=>$_FILES));

        /*$param = [];
        if($post['file_type']==1){
            $param['type'] = 'image'; //图片
        }else if($post['file_type']==2){
            $param['type'] = 'file'; //文件
        }*/



        $controller = new UploadLogic;
       // $post['type'] = $post['type']=='file' ? 'file' : 'image';
        $return = $controller->upload();
        //$this->appReturn(array('code'=>205,'data'=>$return));
        if($return['code']==1){
            $return['code'] = 200;
        }

        $this->appReturn($return);
    }
    
    
    /**
     * 附件信息
     * @param  integer $id [description]
     * @return [type] [description]
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


    /**
     * 上传视频
     */
    public function upload_file(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        // 移动到框架应用根目录/public/uploads/ 目录下
        $fileinfo = $file->getInfo();
        if($file){
            $info = $file->validate(['size'=>52428800,'ext'=>'mp4'])->move(ROOT_PATH . 'public_html' . DS . 'uploads/file');

            $md5  = $info->md5();
            $sha1 = $info->sha1();
            $getSaveName = $info->getSaveName();
            $path = '/uploads/file/'.$getSaveName;
            $path = str_replace("\\", '/', $path);

            if($info){
                $pic['md5']  = $md5;
                $pic['sha1'] = $sha1;
                $picture = db('attachment')->where($pic)->find();
                if($picture){
                    unset($info);
                    @unlink('.'.$path);

                    $result['path'] = config('index_url').$picture['path'];
                    $result['id']   = $picture['id'];
                    $this->appReturn(['code'=>200,'msg'=>'上传成功','data'=>$result]);
                }else{

                    $data['name'] = $fileinfo['name'];
                    $data['path_type'] = 'file';
                    $data['path'] = $path;
                    $data['url']  = $path;
                    $data['md5']  = $md5;
                    $data['sha1'] = $sha1;
                    $data['size'] = $fileinfo['size'];
                    $data['ext']  = $info->getExtension();
                    $data['status'] = 1;
                    $data['create_time'] = mydate();
                    $pic_id = db('attachment')->insertGetId($data);
                    if(!empty($pic_id)){
                        $result['path'] = config('index_url').$data['path'];
                        $result['id']   = $pic_id;
                        $this->appReturn(['code'=>200,'msg'=>'上传成功','data'=>$result]);
                    }else{
                        $this->appReturn(['code'=>201,'msg'=>'上传失败']);
                    }
                }
            }else{
                // 上传失败获取错误信息
                $this->appReturn(['code'=>201,'msg'=>$file->getError()]);
            }
        }
    }

    public function index(){
        return $this->fetch();
    }

}