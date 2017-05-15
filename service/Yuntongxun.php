<?php
/**
 * Created by dh2y
 * email: xiaodai54_long@163.com
 * Date: 2017/4/21 17:26
 * describ: 创蓝253 短信接口
 */

namespace sms\service;

/** .-----------------------------配置说明---------------------------------
 * |    只需要配置 api_account(创蓝账号)和  api_password(创蓝密码)
 * |------------------------------配置方法---------------------------------
 * |   SMS_SDK => array(
 * |        'class' => 'Chuanglan',
 * |        'api_account' => 'demo',
 * |        'api_password'=> '12345'
 * |   )
 * |   new Sms(config('SMS_SDK'))
 * '-------------------------------------------------------------------*/

class Chuanglan
{
   /**
     * @var string
     */
    public $accountSid;
    
    /**
     * @var string
     */
    public $accountToken;
    
    /**
     * @var string
     */
    public $appId;
    
    /**
     * @var string
	 *说明：生成环境请求地址：app.cloopen.com。
     */
    public $serverIp = 'app.cloopen.com';
    
    /**
     * @var string
     */
    public $serverPort;
    
    /**
     * @var string
     */
    public $softVersion;
    
    /**
     * @var string
     */
    public $dataType = 'json';
    
    /**
     * @var string|null
     */
    private $_batch;
    
	 public function __construct(array $config=[])
    {
        $this->appId =$config['appId'];
    }

	
    /**
     * @inheritdoc
     */
    public function sendSMS($mobile, $content)
    {
        throw new \Exception('云通讯不支持直接发送文本！');
    }
    
    /**
     * @inheritdoc
     */
    public function sendByTemplate($mobile, $data, $id)
    {
        
        if ($this->dataType === 'json') {
            $body = json_encode([
                'to' => $mobile,
                'templateId' => $id,
                'appId' => $this->appId,
                'datas' => array_values($data)
            ]);
        } elseif ($this->dataType === 'xml') {
            
            $dataStr = '';
            foreach ($data as $val) {
                $dataStr .= "<data>{$val}</data>";
            }
            
            $body = <<<XML
<TemplateSMS>
    <to>{$mobile}</to> 
    <appId>{$this->appId}</appId>
    <templateId>{$id}</templateId>
    <datas>{$dataStr}</datas>
</TemplateSMS>
XML;
        } else {
            throw new InvalidConfigException('“dataType” 配置不正确。');
        }
        
        $sig = strtoupper(md5($this->accountSid . $this->accountToken . $this->getBatch()));
        $this->url = "https://{$this->serverIp}:{$this->serverPort}/{$this->softVersion}/Accounts/{$this->accountSid}/SMS/TemplateSMS?sig={$sig}";
        $authen = base64_encode($this->accountSid . ':' . $this->getBatch());
        $header = ["Accept:application/{$this->dataType}", "Content-Type:application/{$this->dataType};charset=utf-8", "Authorization:{$authen}"];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        
        $result = curl_exec($ch);
        curl_close($ch);
        
        if (empty($result)) {
            $this->state = '172001';
            $this->message = '网络错误';
        } else {
            if ($this->dataType === 'json') {
                $json = json_decode($result);
                if ($json && is_object($json)) {
                    $this->state = isset($json->statusCode) ? (string) $json->statusCode : null;
                    $this->message = isset($json->statusMsg) ? (string) $json->statusMsg : null;
                }
            } else {
                $xml = simplexml_load_string(trim($result, " \t\n\r"));
                if ($xml && is_object($xml)) {
                    $this->state = isset($xml->statusCode) ? (string) $xml->statusCode : null;
                    $this->message = isset($xml->statusMsg) ? (string) $xml->statusMsg : null;
                }
            }
        }
        
        return $this->state === '000000';
    }
    
    /**
     * 时间戳
     * 
     * @return string
     */
    public function getBatch()
    {
        if ($this->_batch === null) {
            $this->_batch = date('YmdHis');
        }
        
        return $this->_batch;
    }
}