<?php

return [
    'status'=>[
        'title' =>'是否开启短信接口:',
        'type'  =>'radio',
        'options'=>[
            '1'=>'开启',
            '0'=>'关闭',
        ],
        'value'=>'1',
    ],
    'accessKeyId'=>[
        'title'=>'accessKeyId:',
        'type'=>'text',
        'value'=>'',
        'description'=>'请通过<a href="https://ram.console.aliyun.com" target="_blank">RAM管理控制台</a>申请',
    ],
    'accessKeySecret'=>[
        'title' => 'accessKeySecret:',
        'type'  => 'text',
        'value' => '',
        'description'=>'请通过<a href="https://ram.console.aliyun.com" target="_blank">RAM管理控制台</a>申请',
    ],
    'signName'=>[
        'title'=>'签名:',
        'type'=>'text',
        'value'=>'',
        'description'=>'短信认证签名',
    ],
];
