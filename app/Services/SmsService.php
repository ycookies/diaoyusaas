<?php

namespace App\Services;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\libary\Alisms\Alisms;
/**
 * 短信服务
 * @package App\Services
 * anthor Fox
 */
class SmsService extends BaseService {

    /**
     * @param $code 短信验证码
     * @param $msg  短信内容
     * @return array
     */
    public function send($phone){
        $code =  rand(100000,999999);
        $easySms = (new Alisms())->make();
        $res = $easySms->send($phone, [
            'content'  => '',
            'template' => 'SMS_467445308',
            'data' => [
                'code' => $code
            ],
        ]);
        // 这里加上记录异常
        if($res){
            $data = [
                'phone' => $phone,
                'code' => $code,
                'status' => 1,
                'expire' => (time() + 600),
                'response' =>@$res->Message,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $retc = \DB::connection('hotel')->table('sms_codes')->insert($data);
            if($retc){
                return returnData(200,1,[],'发送成功');
            }
        }
        return returnData(10002,0,[],'发送失败');
    }

    // 发送通知
    public function sendNotice($phone,$template_code,$data){
        $easySms = (new Alisms())->make();
        $res = $easySms->send($phone, [
            'content'  => '',
            'template' => $template_code,
            'data' => $data,
        ]);
        addlogs('sms_sendNotice',[$phone,$template_code,$data],$res);
        return $res;
    }

    /**
     * @param $code 短信验证码
     * @param $msg  短信内容
     * @return array
     */
    public function sendPhoneCode($phone){
        $code =  rand(100000,999999);
        $easySms = (new Alisms())->make();
        $res = $easySms->send($phone, [
            'content'  => '',
            'template' => 'SMS_467445308',
            'data' => [
                'code' => $code
            ],
        ]);
        // 这里加上记录异常
        if(!empty($res['aliyun']['status']) && $res['aliyun']['status'] == 'success'){
            $data = [
                'phone' => $phone,
                'code' => $code,
                'status' => 1,
                'expire' => (time() + 600),
                'response' =>json_encode($res),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $retc = \DB::connection('hotel')->table('sms_codes')->insert($data);
            if($retc){
                return returnData(200,1,[],'发送成功');
            }
        }
        return returnData(10002,0,[],'发送失败');
    }

    /**
     * 验证短信验证码是否正确
     * @param $phone
     * @param $code
     * author Fox
     */
    public function isCodeCorrect($phone,$code){

        $sql  = " where phone=".$phone;
        $sql .= " and code=".$code;
        $sql .= " and expire >= ".time();
        /*$sql .= " and check_status=0";
        $sql .= " and status=1";*/
        //$sms_check =  DB()->get('sms_codes','*',['phone'=>$phone,'code'=>$code,'status'=>1,'expire[>=]'=>time()]);
        $where = [];
        $where[] = ['phone','=',$phone];
        $where[] = ['code','=',$code];
        $where[] = ['status','=',1];
        $where[] = ['expire','>=',time()];
        $sms_check = \DB::connection('hotel')->table('sms_codes')->where($where)->first();
        if(!$sms_check){
            return  false;
        }
        \DB::connection('hotel')->table('sms_codes')->where(['id'=>$sms_check->id])->update(['check_status'=>1]);
        return true;
    }
}