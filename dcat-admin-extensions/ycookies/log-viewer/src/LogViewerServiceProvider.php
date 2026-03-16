<?php

namespace Dcat\Admin\LogViewer;

use Dcat\Admin\Extend\ServiceProvider;
use Dcat\Admin\Admin;

class LogViewerServiceProvider extends ServiceProvider
{
	protected $js = [
        'js/index.js',
    ];
	protected $css = [
		'css/index.css',
	];

	public function register()
	{
		//
	}

    // 定义菜单
    protected $menu = [
        [
            'title' => '系统日志',
            'uri'   => 'ycookies/log-viewer',
            'icon'  => 'fa-file-text-o',
        ]
    ];

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
