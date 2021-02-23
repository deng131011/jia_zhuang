<?php

namespace app\admin\controller;


class Appad extends Admin{
    

    function _initialize()
    {
        parent::_initialize();
        $this->appStartad = model('appStartad'); //启动广告模型
        $this->appLoginad = model('appLoginad'); //登录广告模型
        $this->linkType  = [
            1 => '外链接',
            2 => '站内链接'
        ];
    }


    /**
     * App启动广告
     */
    public function startad() {
        $title = "启动广告";
        if (IS_POST) {
            $post = input('param.');

            if(empty($post['longtime'])){
                $this->error('请填写时间！');
            }
            if(empty($post['icon1']) || empty($post['icon2']) || empty($post['icon3']) || empty($post['icon4']) || empty($post['icon5']) || empty($post['icon6'])){
                $this->error('请上传图片！');
            }
            if(empty($post['id'])){
                $post['create_time'] = date('Y-m-d H:i:s');
            }
            //$data里包含主键id，则editData就会更新数据，否则是新增数据
            if ($this->appStartad->editData($post)) {
                $this->success($title.'成功');
            } else {
                $this->error($this->appStartad->getError());
            }

        } else {

            $info =db('app_startad')->where('id',1)->find();
            $info['title'] = $info['title'] != '' ? $info['title']  : '启动广告';

            $return = builder('Form')
                    ->addFormItem('id', 'hidden', 'ID', 'ID')
                    ->addFormItem('title', 'text', '广告名称', '')
                    ->addFormItem('longtime', 'text', '跳转时间', '请填写数字，单位/秒')
                    ->addFormItem('icon1', 'picture', '图片一', '尺寸1080*1920')
                    ->addFormItem('icon2', 'picture', '图片二', '尺寸1536*2048')
                    ->addFormItem('icon3', 'picture', '图片三', '尺寸1125*2436')
                    ->addFormItem('icon4', 'picture', '图片四', '尺寸750*1334')
                    ->addFormItem('icon5', 'picture', '图片五', '尺寸640*1136')
                    ->addFormItem('icon6', 'picture', '图片六', '尺寸640*960')
                    ->setFormData($info)
                    ->addButton('submit')   // 设置表单按钮
                    ->fetch();

            return Iframe()
                    ->setMetaTitle($title)  // 设置页面标题
                    ->content($return);
        }
    }




    /**APP登录广告**/
    public function loginad() {
        $get = $_GET;

        $map = array();
        if(!empty($get['keyword'])){
            $map['title|url'] = array('like','%'.trim($get['keyword']).'%');
        }
        if(!empty($get['linktype'])){
            $map['linktype'] = array('eq',$get['linktype']);
        }

        // 获取所有链接
        list($data_list,$total) = $this->appLoginad->getListByPage($map,true,'sort,create_time desc');


        $content = builder('List')
            ->addTopButton('addnew',['href'=>url('edit_loginad')])    // 添加新增按钮
            ->addTopButton('resume',['model'=>'appLoginad'])  // 添加启用按钮
            ->addTopButton('forbid',['model'=>'appLoginad'])  // 添加禁用按钮
            ->addTopButton('sort',['model'=>'appLoginad'])  // 添加排序按钮
            ->setSearch('请输入关键字')
            ->keyListItem('title', '广告名称')
            ->keyListItem('url', '链接地址','url',['extra_attr'=>'target="_blank"'])
            ->keyListItem('icon', '广告图', 'picture')
            //->keyListItem('type', '类型', 'array',$this->linkType)
            ->keyListItem('sort', '排序')
            ->keyListItem('status', '状态', 'status')
            ->keyListItem('right_button', '操作', 'btn')
            ->setListData($data_list)     // 数据列表
            ->setListPage($total)  // 数据列表分页
            ->addRightButton('edit',['href'=>url('edit_loginad',['id'=>'__data_id__'])])     // 添加编辑按钮
            ->addRightButton('delete',['model'=>'appLoginad'])  // 添加删除按钮
            ->fetch();

        return Iframe()
            ->setMetaTitle('App登录广告')  // 设置页面标题
            ->search([
                ['name'=>'linktype','type'=>'select','title'=>'链接类型','options'=>$this->linkType],
                ['name'=>'keyword','type'=>'text','extra_attr'=>'placeholder="请输入查询关键字"'],
            ])

            ->content($content);
    }


    /**
     * 编辑登录广告
     */
    public function edit_loginad($id=0) {
        $title = $id>0 ? "编辑":"新增";
        $info = array();
        if (IS_POST) {
            $post = input('param.');
            //验证数据
            $checkres = $this->appLoginad->checkform($post);
            if($checkres['code']==201){
                $this->error($checkres['msg']);
            }
            if($post['id']>0){
                $post['update_time'] = date('Y-m-d H:i:s');
            }else{
                $post['create_time'] = date('Y-m-d H:i:s');
            }
            //$data里包含主键id，则editData就会更新数据，否则是新增数据
            if ($this->appLoginad->editData($post)) {
                $this->success($title.'成功', url('loginad'));
            } else {
                $this->error($this->appLoginad->getError());
            }

        } else {

            $info =db('app_loginad')->where('id',$id)->find();

            $return = builder('Form')
                ->addFormItem('id', 'hidden', 'ID', 'ID')
                ->addFormItem('title', 'text', '广告名称', '请输入登录广告名称')
                ->addFormItem('linktype', 'select','链接类型', '',$this->linkType)
                ->addFormItem('url', 'text', '链接地址', '外链接填写带http://的全路径，其他类型请填写对应ID')

                ->addFormItem('sort', 'number', '排序', '按照数值大小的升序进行排序，数值越小越靠前')
                ->addFormItem('icon', 'picture', '广告图片', '尺寸640*1136')
                ->setFormData($info)
                ->addButton('submit')->addButton('back')    // 设置表单按钮
                ->fetch();

            return Iframe()
                ->setMetaTitle($title.'广告')  // 设置页面标题
                ->content($return);
        }
    }


    /**
     * 对链接进行排序
     * @author 心云间、凝听 <981248356@qq.com>
     */
    public function sort($ids = null)
    {
        $builder = builder('Sort');
        if (IS_POST) {
            return $builder->doSort('app_loginad', $ids);
        } else {
            $map['status'] = ['egt', 0];
            $list = $this->appLoginad->getList($map,'id,title,sort', 'sort asc');
            foreach ($list as $key => $val) {
                $list[$key]['title'] = $val['title'];
            }
            $content = $builder
                ->setListData($list)
                ->addButton('submit')->addButton('back')
                ->fetch();

            return Iframe()
                ->setMetaTitle('配置排序')
                ->content($content);
        }
    }

    

}