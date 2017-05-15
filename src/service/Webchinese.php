<?php
/**
 * Created by dh2y
 * email: xiaodai54_long@163.com
 * Date: 2017/4/21 17:26
 * describ: �й����� ���Žӿ�
 */

namespace sms\service;

/** .-----------------------------����˵��---------------------------------
 * |    ֻ��Ҫ���� uid(�й������˺�)��  key(�й���������)
 * |------------------------------���÷���---------------------------------
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
        //�й��������Ͷ��Žӿ�URL, ���ޱ�Ҫ���ò����ɲ����޸�
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
     * ���Ͷ���
	 * @param string $mobile �ֻ�����
     * @param string $content ��������
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
                $this->message = 'û�и��û��˻�';
                break;
            case '-2' :
                $this->message = '�ӿ���Կ����ȷ';
                break;
            case '-21' :
                $this->message = 'MD5�ӿ���Կ���ܲ���ȷ';
                break;
            case '-3' :
                $this->message = '������������';
                break;
            case '-11' :
                $this->message = '���û�������';
                break;
            case '-14' :
                $this->message = '�������ݳ��ַǷ��ַ�';
                break;
            case '-4' :
                $this->message = '�ֻ��Ÿ�ʽ����ȷ';
                break;
            case '-41' :
                $this->message = '�ֻ�����Ϊ��';
                break;
            case '-42' :
                $this->message = '��������Ϊ��';
                break;
            case '-51' :
                $this->message = '����ǩ����ʽ����ȷ';
                break;
            case '-6' :
                $this->message = 'IP����';
                break;
            default :
                $this->message = '���ŷ��ͳɹ�';
                $success = true;
                break;
        }
        
        return array['state'=>$this->state,'message'=>$this->message];
    }
}