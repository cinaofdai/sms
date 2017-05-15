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
    public $config = array(
        //创蓝发送短信接口URL, 如无必要，该参数可不用修改
        'api_send_url' => 'http://vsms.253.com/msg/send/json',

        //创蓝变量短信接口URL, 如无必要，该参数可不用修改
        'API_VARIABLE_URL' => 'http://vsms.253.com/msg/variable/json',

        //创蓝短信余额查询接口URL, 如无必要，该参数可不用修改
        'api_balance_query_url' => 'http://vsms.253.com/msg/balance/json',

        //创蓝账号 替换成你自己的账号
        'api_account' => '',

        //创蓝密码 替换成你自己的密码
        'api_password' => '',
    );


    public function __construct(array $config=[])
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 发送短信
     *
     * @param string $mobile 手机号码
     * @param string $msg 短信内容
     * @param string $needstatus 是否需要状态报告
     * @return mixed
     */
    public function sendSMS($mobile, $msg, $needstatus = 'true')
    {


        //创蓝接口参数
        $postArr = array(
            'account' => $this->config['api_account'],
            'password' => $this->config['api_password'],
            'msg' => urlencode($msg),
            'phone' => $mobile,
            'report' => $needstatus
        );

        $result = $this->curlPost($this->config['api_send_url'], $postArr);
        if (!is_null(json_decode($result))) {
            $result = json_decode($result, true);
        }
        return $result;
    }

    /**
     * 发送变量短信
     * @param string $msg 短信内容
     * @param $params 最多不能超过1000个参数组
     * @return mixed
     */
    public function sendVariableSMS($msg, $params)
    {


        //创蓝接口参数
        $postArr = array(
            'account' => $this->config['api_account'],
            'password' => $this->config['api_password'],
            'msg' => $msg,
            'params' => $params,
            'report' => 'true'
        );

        $result = $this->curlPost($this->config['API_VARIABLE_URL'], $postArr);
        if (!is_null(json_decode($result))) {
            $result = json_decode($result, true);
        }
        return $result;
    }


    /**
     * 查询额度
     *
     *  查询地址
     */
    public function queryBalance()
    {

        //查询参数
        $postArr = array(
            'account' => $this->config['api_account'],
            'password' => $this->config['api_password'],
        );
        $result = $this->curlPost($this->config['api_balance_query_url'], $postArr);

        if (!is_null(json_decode($result))) {
            $result = json_decode($result, true);
        }
        return $result;
    }

    /**
     * 通过CURL发送HTTP请求
     * @param string $url //请求URL
     * @param array $postFields //请求参数
     * @return mixed
     */
    private function curlPost($url, $postFields)
    {
        $postFields = json_encode($postFields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8'
            )
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $ret = curl_exec($ch);
        if (false == $ret) {
            $result = curl_error($ch);
        } else {
            $rsp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 != $rsp) {
                $result = "请求状态 " . $rsp . " " . curl_error($ch);
            } else {
                $result = $ret;
            }
        }
        curl_close($ch);
        return $result;
    }
}