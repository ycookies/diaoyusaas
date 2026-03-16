<?php
namespace App\Services;

use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;
use Swift_Attachment;
use Illuminate\Http\Request;
use Validator;

class EmailService extends BaseService
{
    public function SendEmail(Request $request)
    {
        try {
            $logid = addlogs('Api/SendEmailController/SendEmail',$request->all(),000,175001800);
            $transport = Swift_SmtpTransport::newInstance('smtp.qq.com', 465);
            $transport->setUsername('3664839@qq.com');
            $transport->setPassword('hduzepoujgkdbhjj');
            $transport->setEncryption('ssl');
            $mailer = Swift_Mailer::newInstance($transport);
            $message = Swift_Message::newInstance();
            $message->setFrom(array('3664839@qq.com' => '超光闪电开票'));
            if(!$request->has('send_to')){
                return $this->returnData(40030,0,[],'缺少参数[send_to]');
            }
            if(!$request->has('title')){
                return $this->returnData(40030,0,[],'缺少参数[title]');
            }
            if(!$request->has('content')){
                return $this->returnData(40030,0,[],'缺少参数[content]');
            }
            /*if($request->get('file')==1){
                if(!$request->get('file_path')){
                    return $this->returnData(40030,0,[],'缺少参数[file_path]');
                }
            }*/
           /* $emails=[["email"=>"1x@qq.com","name"=>"asd"],["email"=>"232@qq.com","name"=>"gc1"]];*/
           $emails=$request->get('send_to');
            $rules=[
                "email"=>'required|email',
                'name'=>'required'
            ];
            $messages=[
                "email.required" => '收件人邮箱不能为空',
                'email.email' => '收件人邮箱格式不正确',
                'name.required' => '收件人姓名不能为空',
            ];

            $send_to=[];
            if(is_array($emails)){
                foreach ($emails as $vo){
                    $validate=Validator::make($vo, $rules, $messages);
                    if(!$validate->passes()){
                        return $validate->errors()->all();
                        break;
                    }else{
                        array_push($send_to,[$vo['email']=>$vo['name']]);
                    }
                }
            }else{
                $send_to=[$request->get('send_to')=>"尊敬的用户".$request->get('send_to')];
            }

            $title=$request->get('title');
            $content=$request->get('content');
            $message->setTo($send_to);
            $message->setSubject($title);//邮件标题
            $message->setBody($content, 'text/html', 'utf-8');

               $res = $mailer->send($message);
                \DB::table('api_logs')->where('id',$logid)->update(['result'=>serialize((array)$res)]);
                //ApiLogAdd('Api/SendEmailController/SendEmail',$request->all(),$res,175001800);
               if($res){
                   return returnData(40000,1,['status'=>'success','res'=>$res],'发送邮件成功');
               }else{
                   return returnData(40020,0,['status'=>'error','res'=>$res],'发送邮件失败');
               }
/*
            $message->attach(Swift_Attachment::fromPath('public/test.jpg', 'image/jpeg')->setFilename('rename_pic.jpg'));*/
        } catch (\Exception $exception) {
            Exception_Error_Log(__CLASS__ . ' /SendEmail', $request->all(), $exception, 'exception');
        } catch (\Error $error) {
            Exception_Error_Log(__CLASS__ . '/SendEmail', $request->all(), $error, 'error');
        }

        }

    public function EmailTest(Request $request)
    {
        try {
            return 1;
        } catch (\Exception $exception) {
            Exception_Error_Log(__CLASS__ . ' /EmailTest', $request->all(), $exception, 'exception');
        } catch (\Error $error) {
            Exception_Error_Log(__CLASS__ . '/EmailTest', $request->all(), $error, 'error');
        }
        
        }

}