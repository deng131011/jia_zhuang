<?php
namespace plugins\Alisms;
use app\common\controller\Plugin;

use plugins\Alisms\library\Client;
use plugins\Alisms\library\Request\SendSms;
/**
 * 阿里短信插件
 *LTAIV4UPCjO6nsC5   , gYywsxmKMQuj2xaoUg9eGPKPoFYq2Y
 */
class Index extends Plugin{

    /**
     * @var array 插件钩子
     */
    public $hooks = [
        'sms'
    ];

    /**
     * 插件安装方法
     */
    public function install(){
        return true;
    }

    /**
     * 插件卸载方法
     */
    public function uninstall(){
        return true;
    }

    /**
     * 短信发送函数
     * @param string $sms_data 短信信息结构
     * @$sms_data['RecNum'] 收件人手机号码
     * @$sms_data['code']验证码内容
     * @$sms_data['SmsFreeSignName']短信签名
     * @$sms_data['SmsTemplateCode']短信模版ID
     * @return boolean
     */
    function sms($params=[]){
        $alisms_config = $this->getConfig('Alisms');
        if($alisms_config['status']){
            $config = [
                'accessKeyId'     => $alisms_config['accessKeyId'],
                'accessKeySecret' => $alisms_config['accessKeySecret'],
            ];

            $client  = new Client($config);
            $sendSms = new SendSms;
            $sendSms->setPhoneNumbers($params['mobile']);
            $sendSms->setSignName($alisms_config['signName']);
            $sendSms->setTemplateCode($params['template']);
             // 可选，设置模板参数
            if (!empty($params['templateParam'])) {
                //模版参数为数组格式
                $sendSms->setTemplateParam($params['templateParam']);
            }
            // 可选，设置流水号
            if (!empty($params['outId'])) {
                $sendSms->setOutId($params['outId']);
            }
            
            $result = $client->execute($sendSms);
            if($result->Code=='OK'){
                return true;
            } else{
                return false;
            }
        } else{
            return false;
        }
    }
}
