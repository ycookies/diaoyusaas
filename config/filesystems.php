<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        'jinjian' => [
            'driver' => 'local',
            'root' => public_path('jinjiandoc'),
            'visibility' => 'public',
            'url' => env('APP_URL').'/jinjiandoc',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
        ],
        'admin' => [
            'driver' => 'local',
            'root' => public_path('uploads'),
            'visibility' => 'public',
            'url' => env('APP_URL').'/uploads',
        ],
        'merchant' => [
            'driver' => 'local',
            'root' => public_path('uploads/merchant'),
            'visibility' => 'public',
            'url' => env('APP_URL').'/uploads/merchant',
        ],
        'hotel_143' => [
            'driver' => 'local',
            'root' => public_path('uploads/hotel/143'),
            'visibility' => 'public',
            'url' => env('APP_URL').'/uploads/hotel/143',
        ],
        'hotel_225' => [
            'driver' => 'local',
            'root' => public_path('uploads/hotel/225'),
            'visibility' => 'public',
            'url' => env('APP_URL').'/uploads/hotel/225',
        ],
        'hotel_226' => [
            'driver' => 'local',
            'root' => public_path('uploads/hotel/226'),
            'visibility' => 'public',
            'url' => env('APP_URL').'/uploads/hotel/226',
        ],
        'hotel_227' => [
            'driver' => 'local',
            'root' => public_path('uploads/hotel/227'),
            'visibility' => 'public',
            'url' => env('APP_URL').'/uploads/hotel/227',
        ],
        'hotel_228' => [
            'driver' => 'local',
            'root' => public_path('uploads/hotel/228'),
            'visibility' => 'public',
            'url' => env('APP_URL').'/uploads/hotel/228',
        ],
        'hotel_229' => [
            'driver' => 'local',
            'root' => public_path('uploads/hotel/229'),
            'visibility' => 'public',
            'url' => env('APP_URL').'/uploads/hotel/229',
        ],
        'hotel_230' => [
            'driver' => 'local',
            'root' => public_path('uploads/hotel/230'),
            'visibility' => 'public',
            'url' => env('APP_URL').'/uploads/hotel/230',
        ],
        'hotel_231' => [
            'driver' => 'local',
            'root' => public_path('uploads/hotel/1440'),
            'visibility' => 'public',
            'url' => env('APP_URL').'/uploads/hotel/1440',
        ],
        'qiniu' => [
            'driver'  => 'qiniu',
            'domains' => [
                'default'   => 'rsunleljl.hd-bkt.clouddn.com', //你的七牛域名
                'https'     => '',         //你的HTTPS域名
                'custom'    => '',                //你的自定义域名
            ],
            'access_key'=> env('QINIU_ACCESS_KEY', ''),  //AccessKey
            'secret_key'=> env('QINIU_SECRET_KEY', ''),  //SecretKey
            'bucket'    => 'dsxia',  //Bucket名字
            'notify_url'=> '',  //持久化处理回调地址
            'url'       => '',  // 填写文件访问根url
        ],
        'oss' => [
            'driver' => 'oss',
            'root' => '', // 设置上传时根前缀
            'access_key' => env('OSS_ACCESS_KEY', ''),
            'secret_key' => env('OSS_SECRET_KEY', ''),
            'endpoint'   => 'https://oss-cn-guangzhou.aliyuncs.com', // 使用 ssl 这里设置如: https://oss-cn-beijing.aliyuncs.com
            'bucket'     => 'rb-booking',
            'isCName'    => env('OSS_IS_CNAME', false),
            'host' => 'https://rb-booking.oss-cn-guangzhou.aliyuncs.com',

            /*'driver' => 'oss',
            'root' => '', // 设置上传时根前缀
            'access_key' => env('OSS_accessKeyId'),
            'secret_key' => env('OSS_accessKeySecret'),
            'endpoint'   => env('OSS_region'), // 使用 ssl 这里设置如: https://oss-cn-beijing.aliyuncs.com
            'bucket'     => env('OSS_bucket'),
            'isCName'    => env('OSS_IS_CNAME', false), // 如果 isCname 为 false，endpoint 应配置 oss 提供的域名如：`oss-cn-beijing.aliyuncs.com`，否则为自定义域名，，cname 或 cdn 请自行到阿里 oss 后台配置并绑定 bucket
            // 如果有更多的 bucket 需要切换，就添加所有bucket，默认的 bucket 填写到上面，不要加到 buckets 中
            'buckets'=>[
                'test'=>[
                    'access_key' => env('OSS_ACCESS_KEY'),
                    'secret_key' => env('OSS_SECRET_KEY'),
                    'bucket'     => env('OSS_TEST_BUCKET'),
                    'endpoint'   => env('OSS_TEST_ENDPOINT'),
                    'isCName'    => env('OSS_TEST_IS_CNAME', false),
                ],
            ],*/
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
