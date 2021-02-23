<?php

namespace app\admin\validate;

use think\Validate;

class Link extends Validate
{
    // 验证规则
    protected $rule = [
        'title'      => 'require|length:1,80|unique:links',
        'url'        => 'require|url|length:1,180|unique:links'
    ];

    protected $message = [
        'title.require'      => '站点名称不能为空',
        'title.length'      => '站点名称长度为1-80个字符',
        'title.unique'       => '标题已经存在',
        'url.require'        => '链接不能为空',
        'url.url'           => '链接格式不正确',
        'url.length'        => '链接过长',
        'url.unique'         => '链接已经存在',
    ];

    protected $scene=[
        'edit' => ['title','url'],
    ];
}