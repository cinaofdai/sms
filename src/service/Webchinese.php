<?php
/**
 * Created by dh2y
 * email: xiaodai54_long@163.com
 * Date: 2017/4/21 17:26
 * describ: 中国网建 短信接口
 */

namespace sms\service;

/** .-----------------------------配置说明---------------------------------
 * |    只需要配置 uid(中国网建账号)和  key(中国网建密码)
 * |------------------------------配置方法---------------------------------
 * |   SMS_SDK => array(
 * |        'class' => 'Webchinese',
 * |        'uid' => 'demo',
 * |        'key'=> '12345'
 * |   )
 * |   new Sms(config('SMS_SDK'))
 * '-------------------------------------------------------------------*/

class Webchinese
{
    public $config = array(
        //中国网建发送短信接口URL, 如无必要，该参数可不用修改
        'url' => 'http://utf8.sms.webchinese.cn/',
		'uid' => '',
		'key' => ''

    );

	private $state;
	private $message;

    public function __construct(array $config=[])
    {
        $this->config = array_merge($this->config, $config);
    }

    
    /**
     * 发送短信
	 * @param string $mobile 手机号码
     * @param string $content 短信内容
     */
    public function sendSMS($mobile, $content)
    {
        
        $data = [
            'uid' => $this->config['uid'],
            'key' => $this->config['key'],
            'smsMob' => $mobile,
            'smsText' => $content
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->config['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        
        $this->state = (string) curl_exec($ch);
        curl_close($ch);
        
        $success = false;
        switch ($this->state) {
            case '' :
            case '-1' :
                $this->message = '没有该用户账户';
                break;
            case '-2' :
                $this->message = '接口密钥不正确';
                break;
            case '-21' :
                $this->message = 'MD5接口密钥加密不正确';
                break;
            case '-3' :
                $this->message = '短信数量不足';
                break;
            case '-11' :
                $this->message = '该用户被禁用';
                break;
            case '-14' :
                $this->message = '短信内容出现非法字符';
                break;
            case '-4' :
                $this->message = '手机号格式不正确';
                break;
            case '-41' :
                $this->message = '手机号码为空';
                break;
            case '-42' :
                $this->message = '短信内容为空';
                break;
            case '-51' :
                $this->message = '短信签名格式不正确';
                break;
            case '-6' :
                $this->message = 'IP限制';
                break;
            default :
                $this->message = '短信发送成功';
                $success = true;
                break;
        }
        
        return array['state'=>$this->state,'message'=>$this->message];
    }
}