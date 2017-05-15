<?php
/**
 * Created by dh2y
 * email: xiaodai54_long@163.com
 * Date: 2017/4/21 17:08
 * describ: 短信发送扩展
 */

namespace sms;


class Sms
{
    protected static $config = [];

    public function __construct( array $config = [])
    {
        if(empty( $config )&& function_exists( 'C' ) ){
            $this->config = C('SMS_SDK');
        }
		
		if ( ! empty( $config ) ) {
			self::$config = $config;
		}
		
    }

    /**
     * 发送短信
     * @param $mobile 手机号
     * @param $msg  发送内容
     * @return mixed
     * @throws \Exception
     */
    public function sendSMS($mobile, $msg){
        $class = '\\sms\\service\\'. ucfirst($this->config['class']);
        if(class_exists($class)){
            $sms  =   new $class(self::$config);
            return  $sms->sendSMS($mobile, $msg);
        }else{
            // 类没有定义
            throw new \Exception('没有这个短信服务商');
        }
    }

    /**
     * @param $name
     * @param $args
     * @return mixed
     * @throws \Exception
     */
    function __call ($name, $args ){
        $class = '\\sms\\service\\'. ucfirst($this->config['class']);

        if(class_exists($class)){
            $sms  =   new $class(self::$config);
            if (method_exists($sms,$name)) {
                return call_user_func_array(array($sms,$name),$args);
            }else{
                throw new \Exception('此短信服务商没有定义这个接口');
            }
        }else{
            // 类没有定义
            throw new \Exception('没有这个短信服务商');
        }
    }


}