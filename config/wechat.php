<?php

/*
 * This file is part of the overtrue/laravel-wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

return [
    /*
     * 默认配置，将会合并到各模块中
     */
    'defaults'         => [
        /*
         * 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
         */
        'response_type'     => 'array',

        /*
         * 使用 Laravel 的缓存系统
         */
        'use_laravel_cache' => false,

        /*
         * 日志配置
         *
         * level: 日志级别，可选为：
         *                 debug/info/notice/warning/error/critical/alert/emergency
         * file：日志文件位置(绝对路径!!!)，要求可写权限
         */
        'log'               => [
            'level' => env('WECHAT_LOG_LEVEL', 'debug'),
            'file'  => env('WECHAT_LOG_FILE', storage_path('logs/wechat.log')),
        ],
    ],

    /*
     * 路由配置
     */
    'route'            => [
        /*
         * 开放平台第三方平台路由配置
         */
        // 'open_platform' => [
        //     'uri' => 'serve',
        //     'action' => Overtrue\LaravelWeChat\Controllers\OpenPlatformController::class,
        //     'attributes' => [
        //         'prefix' => 'open-platform',
        //         'middleware' => null,
        //     ],
        // ],
    ],

    /**
     * 'app_id'  => 'wx6b5d95654b6adf14', // AppID 钓赛通公众号
    'secret'  => 'b562b9f113b7e796b60e2c1f2f7865c2', // AppSecret
    'token'   => 'bVzKtSxg1oig9q5Z1OcRfzMlHuXoh', // Token
    'aes_key' => 'Q69l7SaybEI6frOwqqwZ6yYVzDHD5DCWSOLrjMLf8el', // EncodingAESKey，安全模式下请一定要填写！！！

     */
    /*
     * 公众号
     */
    'official_account' => [
        'default' => [
            'app_id'  => env('WECHAT_OFFICIAL_ACCOUNT_APPID', 'wx6b5d95654b6adf14'),         // AppID
            'secret'  => env('WECHAT_OFFICIAL_ACCOUNT_SECRET', 'b562b9f113b7e796b60e2c1f2f7865c2'),    // AppSecret
            'token'   => env('WECHAT_OFFICIAL_ACCOUNT_TOKEN', 'bVzKtSxg1oig9q5Z1OcRfzMlHuXoh'),           // Token
            'aes_key' => env('WECHAT_OFFICIAL_ACCOUNT_AES_KEY', 'Q69l7SaybEI6frOwqqwZ6yYVzDHD5DCWSOLrjMLf8el'),                 // EncodingAESKey

            /*
             * OAuth 配置
             *
             * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
             * callback：OAuth授权完成后的回调页地址(如果使用中间件，则随便填写。。。)
             * enforce_https：是否强制使用 HTTPS 跳转
             */
            // 'oauth'   => [
            //     'scopes'        => array_map('trim', explode(',', env('WECHAT_OFFICIAL_ACCOUNT_OAUTH_SCOPES', 'snsapi_userinfo'))),
            //     'callback'      => env('WECHAT_OFFICIAL_ACCOUNT_OAUTH_CALLBACK', '/examples/oauth_callback.php'),
            //     'enforce_https' => true,
            // ],
        ],
    ],

    'gzh2' => [
        'default' => [
            'app_id'  => 'wxb66f6b5fdaa4ab7e',         // AppID
            'secret'  => '0505cc79906feea26f6e07592728b482',    // AppSecret
            'token'   => 'omJNpZEhZeHj1ZxFECKkP48B5VFbk1HP',           // Token
            'aes_key' => 'hkPTLgbZxR1V6XU4dZU40e8t46ncwMvGg9zheRK3eBK',                 // EncodingAESKey
            'response_type' => 'array',
            'log' => [
                'default' => 'dev', // 默认使用的 channel，生产环境可以改为下面的 prod
                'channels' => [
                    // 测试环境
                    'dev' => [
                        'driver' => 'single',
                        'path' => '/tmp/easywechat.log',
                        'level' => 'debug',
                    ],
                    // 生产环境
                    'prod' => [
                        'driver' => 'daily',
                        'path' => '/tmp/easywechat.log',
                        'level' => 'info',
                    ],
                ],
            ],
            /**
             * 接口请求相关配置，超时时间等，具体可用参数请参考：
             * http://docs.guzzlephp.org/en/stable/request-config.html
             *
             * - retries: 重试次数，默认 1，指定当 http 请求失败时重试的次数。
             * - retry_delay: 重试延迟间隔（单位：ms），默认 500
             * - log_template: 指定 HTTP 日志模板，请参考：https://github.com/guzzle/guzzle/blob/master/src/MessageFormatter.php
             */
            'http' => [
                'max_retries' => 1,
                'retry_delay' => 500,
                'timeout' => 30,
                // 'base_uri' => 'https://api.weixin.qq.com/', // 如果你在国外想要覆盖默认的 url 的时候才使用，根据不同的模块配置不同的 uri
            ],
            'oauth' => [
                'scopes'   => ['snsapi_userinfo'],
                'callback' => '/examples/oauth_callback.php',
            ],
            /*
             * OAuth 配置
             *
             * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
             * callback：OAuth授权完成后的回调页地址(如果使用中间件，则随便填写。。。)
             * enforce_https：是否强制使用 HTTPS 跳转
             */
            // 'oauth'   => [
            //     'scopes'        => array_map('trim', explode(',', env('WECHAT_OFFICIAL_ACCOUNT_OAUTH_SCOPES', 'snsapi_userinfo'))),
            //     'callback'      => env('WECHAT_OFFICIAL_ACCOUNT_OAUTH_CALLBACK', '/examples/oauth_callback.php'),
            //     'enforce_https' => true,
            // ],
        ],
    ],

    /*
     * 开放平台第三方平台
     */
    // 'open_platform' => [
    //     'default' => [
    //         'app_id'  => env('WECHAT_OPEN_PLATFORM_APPID', ''),
    //         'secret'  => env('WECHAT_OPEN_PLATFORM_SECRET', ''),
    //         'token'   => env('WECHAT_OPEN_PLATFORM_TOKEN', ''),
    //         'aes_key' => env('WECHAT_OPEN_PLATFORM_AES_KEY', ''),
    //     ],
    // ],

    /*
     * 小程序
     */
    // 'mini_program' => [
    //     'default' => [
    //         'app_id'  => env('WECHAT_MINI_PROGRAM_APPID', ''),
    //         'secret'  => env('WECHAT_MINI_PROGRAM_SECRET', ''),
    //         'token'   => env('WECHAT_MINI_PROGRAM_TOKEN', ''),
    //         'aes_key' => env('WECHAT_MINI_PROGRAM_AES_KEY', ''),
    //     ],
    // ],

    /*
     * 微信支付
     */
    // 'payment' => [
    //     'default' => [
    //         'sandbox'            => env('WECHAT_PAYMENT_SANDBOX', false),
    //         'app_id'             => env('WECHAT_PAYMENT_APPID', ''),
    //         'mch_id'             => env('WECHAT_PAYMENT_MCH_ID', 'your-mch-id'),
    //         'key'                => env('WECHAT_PAYMENT_KEY', 'key-for-signature'),
    //         'cert_path'          => env('WECHAT_PAYMENT_CERT_PATH', 'path/to/cert/apiclient_cert.pem'),    // XXX: 绝对路径！！！！
    //         'key_path'           => env('WECHAT_PAYMENT_KEY_PATH', 'path/to/cert/apiclient_key.pem'),      // XXX: 绝对路径！！！！
    //         'notify_url'         => 'http://example.com/payments/wechat-notify',                           // 默认支付结果通知地址
    //     ],
    //     // ...
    // ],

    /*
     * 企业微信
     */
    // 'work' => [
    //     'default' => [
    //         'corp_id' => 'xxxxxxxxxxxxxxxxx',
    //         'agent_id' => 100020,
    //         'secret'   => env('WECHAT_WORK_AGENT_CONTACTS_SECRET', ''),
    //          //...
    //      ],
    // ],
    // 小程序配置 钓赛通小程序
    'min1' => [
        'app_id' => 'wx8c3d9b0bbf9272bc', // 支付小程序的app_id 不是公众号ID
        'secret' => 'a0c4d3ff475f84f695429891137a0a92', // 支付小程序的secret 不是公众号secret
        'mch_id'     => '1437656102', // 小程序绑定微信支付商户号
        'key'        => 'YKAr9V0IdHl4kvs0CMQ2DTVluSZROlYj', // 小程序绑定微信支付商户号 密钥
        'cert_path'  => '/cert/wxpay/cert2/apiclient_cert.pem', // XXX: 绝对路径！！！！ // 小程序绑定微信支付商户号 证书
        'key_path'   => '/cert/wxpay/cert2/apiclient_key.pem',      // XXX: 绝对路径！！！！ // 小程序绑定微信支付商户号 证书
        'notify_url' => 'https://asks.saishiyun.net/api/min-program/wxPayNotifyWxf0747582a6796ddf',     // 这个小程序支付对应的支付通知地址
        // 下面为可选项
        // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
        'response_type' => 'array',

        'log' => [
            'level' => 'debug',
            'file' => storage_path('logs/easywechat-dev.log'),
        ],
    ],
    // 融宝科技 服务商
    'min2' => [
        'app_id' => 'wxb66f6b5fdaa4ab7e', // 公众号ID
        'secret' => 'FbGgm3Y85Z7i0zmOZbxCfV4DTAPns6lO', // 公众号IDsecret
        'mch_id'     => '1566291601', // 服务商 商户号
        'key'        => 'FbGgm3Y85Z7i0zmOZbxCfV4DTAPns6lO', // 商户号 密钥
        'v3_secret_key'=> 'EToRd3MnsaANSZFzbcrOiR3Q2c01PhdS', // APIv3 密钥
        'serial_no' => '2A5F5D68EE387EACB7150C3AAC846DB3D71EB810', // 证书序列号
        //'serial_no' => '7C5B67C0DA3FD8D3D4EE897AC1BBC34186A7F199',
        //'cert_path'  => '/cert/wxpay/cert2/apiclient_cert.pem', // XXX: 绝对路径！！！！ // 小程序绑定微信支付商户号 证书
        //'key_path'   => '/cert/wxpay/cert2/apiclient_key.pem',      // XXX: 绝对路径！！！！ // 小程序绑定微信支付商户号 证书
        'cert_path'  => '/www/wwwroot/hotel.rongbaokeji.com/config/apiclient_cert.pem', // XXX: 绝对路径！！！！ // 小程序绑定微信支付商户号 证书
        'key_path'   => '/www/wwwroot/hotel.rongbaokeji.com/config/apiclient_key.pem',      // XXX: 绝对路径！！！！ // 小程序绑定微信支付商户号 证书
        'platform_cert_path'   => '/www/wwwroot/hotel.rongbaokeji.com/config/cert.pem',      // 微信支付平台证书
        'platform_pub_id' => 'PUB_KEY_ID_0115662916012025011400326400001256', // 微信支付公钥ID
        'platform_pub_cert' => '/www/wwwroot/hotel.rongbaokeji.com/config/pub_key.pem',// 微信支付公钥
        'platform_certs' => [
            '7C5B67C0DA3FD8D3D4EE897AC1BBC34186A7F199' => '/www/wwwroot/hotel.rongbaokeji.com/config/cert.pem',
            '2B452ED24FF16544CB8F606988422FE6234494F6' => '/www/wwwroot/hotel.rongbaokeji.com/config/cert2.pem',
            'PUB_KEY_ID_0115662916012025011400326400001256'=> '/www/wwwroot/hotel.rongbaokeji.com/config/pub_key.pem',
        ],
        'notify_url' => 'https://hotel.rongbaokeji.com/api/min-program/wxPayNotifyWxf0747582a6796ddf',     // 这个小程序支付对应的支付通知地址
        // 下面为可选项
        // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
        'response_type' => 'array',

        'log' => [
            'level' => 'debug',
            'file' => storage_path('logs/easywechat-dev.log'),
        ],
    ],
];
