<?php

namespace Dcat\Admin\Scheduling;

use Dcat\Admin\Extend\ServiceProvider;
use Dcat\Admin\Admin;

class SchedulingServiceProvider extends ServiceProvider
{
	protected $js = [
        'js/index.js',
        'nprogress/nprogress.js'
    ];
	protected $css = [
		'css/index.css',
        'nprogress/nprogress.css'
	];

	public function register()
	{
		//
	}
    protected $menu = [
        [
            'title' => '定时执行管理',
            'uri'   => 'scheduling',
            'icon'  => 'feather icon-clock',
        ],
    ];

	public function init()
	{
		parent::init();
        Admin::requireAssets('@ycookies.scheduling');
		//
        //Scheduling::boot();
		
	}

	public function settingForm()
	{
		return new Setting($this);
	}
}
