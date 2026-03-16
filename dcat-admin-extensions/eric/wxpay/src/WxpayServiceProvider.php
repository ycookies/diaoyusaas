<?php

namespace Dcat\Admin\Wxpay;

use Dcat\Admin\Extend\ServiceProvider;
use Dcat\Admin\Admin;

class WxpayServiceProvider extends ServiceProvider
{
	protected $js = [
        'js/index.js',
    ];
	protected $css = [
		'css/index.css',
	];

    // 定义菜单
    protected $menu = [
        [
            'title' => '微信公众号',
            'uri'   => '',
            'icon'  => 'feather icon-x',
        ],
        [
            'parent' => '微信公众号', // 指定父级菜单
            'title'  => '公众号支付',
            'uri'    => 'wxgzh/wxpay',
        ]
    ];

	public function register()
	{
		//
	}

	public function init()
	{
		parent::init();

		//
		
	}

	public function settingForm()
	{
		return new Setting($this);
	}
}
