<?php

namespace App\Merchant\Controllers;

use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Admin;
use Illuminate\Http\Request;
use Dcat\Admin\Traits\HasUploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
// 上传
class UploadController extends AdminController
{
    use HasUploadedFile;

    public function imgs(Request $request){
        $disk = Storage::disk('oss');
        // 获取上传的文件
        $file = $this->file();
        // 获取上传的字段名称
        $column = $this->uploader()->upload_column;
        // 上传
        $dir = 'taskImages';
        $uuids = date('md') . strtolower(Str::random(6));
        $newName = $column.'-'.$uuids.'.'.$file->getClientOriginalExtension();

        $path = "{$dir}/$newName";

        $result = $disk->putFileAs('', $file,$path);

        return $result
            ? $this->responseUploaded($disk->url($path), $disk->url($path))
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
        $uuids = date('md') . strtolower(Str::random(6));
        $newName = $column.'-'.$uuids.'.'.$file->getClientOriginalExtension();

        $path = "{$dir}/$newName";

        $result = $disk->putFileAs('', $file,$path);

        return $result
            ? $this->responseUploaded($disk->url($path), $disk->url($path))
            : $this->responseErrorMessage('文件上传失败');

    }

    public function files(Request $request){
        $disk = Storage::disk('oss');
        // 获取上传的文件
        $file = $this->file();
        // 获取上传的字段名称
        $column = $this->uploader()->upload_column;
        // 上传
        $dir = 'taskFile';
        $uuids = date('md') . strtolower(Str::random(6));
        $newName = $column.'-'.$uuids.'.'.$file->getClientOriginalExtension();

        $path = "{$dir}/$newName";

        $result = $disk->putFileAs('', $file,$path);

        return $result
            ? $this->responseUploaded($disk->url($path), $disk->url($path))
            : $this->responseErrorMessage('文件上传失败');

    }


    //七牛云  图片上传
    public function qiniu_imgs(Request $request){
        $disk = $this->disk('qiniu');

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
        $uuids = date('md') . strtolower(Str::random(6));
        $newName = $column.'-'.$uuids.'.'.$file->getClientOriginalExtension();

        $result = $disk->putFileAs($dir, $file, $newName);

        $path = "{$dir}/$newName";

        return $result
            ? $this->responseUploaded($disk->url($path), $disk->url($path))
            : $this->responseErrorMessage('文件上传失败');
    }

    // 七牛云 文件上传
    public function qiniu_files(Request $request){
        $disk = $this->disk('qiniu');

        // 判断是否是删除文件请求
        if ($this->isDeleteRequest()) {
            // 删除文件并响应
            return $this->deleteFileAndResponse($disk);
        }

        // 获取上传的文件
        $file = $this->file();

        // 获取上传的字段名称
        $column = $this->uploader()->upload_column;

        $dir = 'file';
        $uuids = date('md') . strtolower(Str::random(6));
        $newName = $column.'-'.$uuids.'.'.$file->getClientOriginalExtension();

        $result = $disk->putFileAs($dir, $file, $newName);

        $path = "{$dir}/$newName";

        return $result
            ? $this->responseUploaded($disk->url($path), $disk->url($path))
            : $this->responseErrorMessage('文件上传失败');
    }
}
