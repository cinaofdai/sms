<?php
/**
 * Created by dh2y
 * email: xiaodai54_long@163.com
 * Date: 2017/4/21 17:26
 * describ: ÔÆÆ¬ÍøÂç ¶ÌĞÅ½Ó¿Ú
 */

namespace sms\service;

/** .-----------------------------ÅäÖÃËµÃ÷---------------------------------
 * |    Ö»ĞèÒªÅäÖÃapikey(ÔÆÆ¬ÍøÂç)
 * |------------------------------ÅäÖÃ·½·¨---------------------------------
 * |   SMS_SDK => array(
 * |        'class' => 'Yunpian',
 * |        'apikey'=> '12345'
 * |   )
 * |   new Sms(config('SMS_SDK'))
 * '-------------------------------------------------------------------*/

class Yunpian
{
    public $config = array(
        'url' => 'http://yunpian.com/v1/sms/send.json',
		'apikey' => ''
    );
	private $state;
	private $message;

    public function __construct(array $config=[])
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * @inheritdoc
     */
    public function sendSMS($mobile, $content)
    {
       
        $data = [
            'apikey' => $this->$config['apikey'],
            'mobile' => $mobile,
            'text' => $content
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->$config['url']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        
        $result = curl_exec($ch);
        curl_close($ch);
        
        $json = json_decode($result);
        if ($json && is_object($json)) {
            $this->state = isset($json->code) ? (string) $json->code : null;
            $this->message = isset($json->msg) ? (string) $json->msg : null;
        }
        
        return array['state'=>$this->state,'message'=>$this->message];
    }
    
}