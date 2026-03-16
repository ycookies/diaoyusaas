<?php
namespace App\Merchant\Extensions\Tools;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;

class PriceSubMenu extends AbstractTool
{
    protected function script()
    {
        $url = Request::fullUrlWithQuery(['gender' => '_gender_']);

        return <<<EOT

$('input:radio.user-gender').change(function () {

    var url = "$url".replace('_gender_', $(this).val());

    $.pjax({container:'#pjax-container', url: url });

});

EOT;
    }

    public function render()
    {
        Admin::script($this->script());
        $data['submenu'] = [
            [
                'menu_name' => '房价日历',
                'uri' => '#',
            ],
            [
                'menu_name' => '房价设置',
                'uri' => '#',
            ],
            [
                'menu_name' => '批量改房价',
                'uri' => '#',
            ]
        ];
        $options = [
            '1'   => '',
            '2'     => 'Male',
            '3'     => 'Female',
        ];

        return view('admin.tools.gender', $data);
    }
}