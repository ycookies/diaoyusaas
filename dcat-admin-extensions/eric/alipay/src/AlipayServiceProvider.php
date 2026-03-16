<?php

namespace Dcat\Admin\Alipay;

use Dcat\Admin\Extend\ServiceProvider;
use Dcat\Admin\Admin;

class AlipayServiceProvider extends ServiceProvider
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
            'title' => '支付宝支付',
            'uri'   => 'ali/alipay',
            'icon'  => 'feather icon-x',
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
