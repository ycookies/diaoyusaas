<?php

namespace App\Admin\Controllers;

use Dcat\Admin\Traits\HasUploadedFile;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UploadsController extends Controller
{
    use HasUploadedFile;

    public function handle()
    {
        $disk = $this->disk('jinjian');

        // 判断是否是删除文件请求
        if ($this->isDeleteRequest()) {
            // 删除文件并响应
            return $this->deleteFileAndResponse($disk);
        }

        // 获取上传的文件
        $file = $this->file();

        // 获取上传的字段名称
        $column = $this->uploader()->upload_column;

        $dir = 'images';
        $newName = $column.'-'.time().'.'.$file->getClientOriginalExtension();

        $result = $disk->putFileAs($dir, $file, $newName);

        $path = "{$dir}/$newName";

        return $result
            ? $this->responseUploaded($path, $disk->url($path))
            : $this->responseErrorMessage('文件上传失败');
    }

    public function uploadsWeb()
    {
        $disk = $this->disk('public');

        // 判断是否是删除文件请求
        if ($this->isDeleteRequest()) {
            // 删除文件并响应
            return $this->deleteFileAndResponse($disk);
        }

        // 获取上传的文件
        $file = $this->file();

        // 获取上传的字段名称
        $column = $this->uploader()->upload_column;

        $dir = 'images';
        $newName = $column.'-'.time().'.'.$file->getClientOriginalExtension();

        $result = $disk->putFileAs($dir, $file, $newName);

        $path = "{$dir}/$newName";

        return $result
            ? $this->responseUploaded($path, $disk->url($path))
            : $this->responseErrorMessage('文件上传失败');
    }

    public function storage(Request $request){
        $disk = Storage::disk('public');
        // 获取上传的文件
        $file = $this->file();
        // 获取上传的字段名称
        $column = $this->uploader()->upload_column;
        // 上传
        $dir = 'overall';
        if (!is_dir(public_path($dir))) {
            mkdir(public_path($dir),0777,true);
        }
        $uuids = date('md') . strtolower(Str::random(6));
        $newName = $column.'-'.$uuids.'.'.$file->getClientOriginalExtension();

        $path = "{$dir}/$newName";

        $result = $disk->putFileAs('', $file,$path);

        return $result
            ? $this->responseUploaded($disk->url($path), $disk->url($path))
            : $this->responseErrorMessage('文件上传失败');

    }

    // 上传 微信验证文件
    public function verifyFile(Request $request){
        $disk = Storage::disk('public_base');
        // 获取上传的文件
        $file = $this->file();
        $file_name = $file->getClientOriginalName();
        $file_extension = $file->getClientOriginalExtension();
        if($file_extension != 'txt'){
            $this->responseErrorMessage('只能上传txt文件');
        }
        // 获取上传的字段名称
        $column = $this->uploader()->upload_column;
        // 上传
        //$dir = '';
    
        $path = public_path($file_name);
        if(file_exists($path)){
            unlink($path);
        }
        $result = $disk->putFileAs('', $file,$file_name);

        return $result
            ? $this->responseUploaded($disk->url($file_name), $disk->url($file_name))
            : $this->responseErrorMessage('文件上传失败');

    }
}
