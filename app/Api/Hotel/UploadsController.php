<?php

namespace App\Api\Hotel;

use App\Admin;
use App\Models\Hotel\Topic;
use App\Models\Hotel\TopicType;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;


/**
 * 上传
 */
class UploadsController extends BaseController {

    /**
     * 上传图片
     * @desc 上传图片
     * @return int list 列表
     */
    public function upimg(Request $request) {
        $file = $request->file('file');
        $up_type = $request->file('up_type');
        $path = $file->path();
        $extension = $file->extension();
        $save_path = public_path('uploads/images/');

        $new_filename = time(). '.'.$extension;
        if($up_type = 'user-avatar'){
            $new_filename = 'wx-avatar-'.time() . '.'.$extension;
        }
        if($up_type = 'pingjia-img'){
            $new_filename = 'pingjia-img-'.time() . '.'.$extension;
        }
        $imgPath = $save_path.$new_filename;
        $new_path = env('APP_URL').'/uploads/images/'.$new_filename;
        if (move_uploaded_file($path, $imgPath)) {
            if(!file_exists($imgPath)){
                return returnData(204,0,[],'上传失败');
            }
        }

        //$new_path = $file->storeAs($save_path, time().'.'.$extension);
        //info([$path,$extension,$new_path]);
        return returnData(200,1,['web_url'=>$new_path],'ok');
    }

    /**
     * 上传文件
     * @desc 上传文件
     */
    public function upfile(Request $request) {

        return returnData(200,0,[],'ok');
    }

}
