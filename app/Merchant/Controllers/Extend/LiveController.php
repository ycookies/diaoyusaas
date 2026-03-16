<?php
/**
 * @copyright ©2023 杨光
 * @author 杨光
 * @link https://www.saishiyun.net/
 * @contact wx:Q3664839
 * Created by Phpstorm
 * 学习永无止镜 践行开源公益
 */
 
namespace App\Merchant\Controllers\Extend;

use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Widgets\Alert;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Widgets\Form as WidgetsForm;
use Dcat\Admin\Widgets\Modal;
use Dcat\Admin\Widgets\Tab;
use Dcat\Admin\Http\Controllers\AdminController;
use App\Services\NuonuoService;
use Illuminate\Http\Request;
use App\Mail\InvoiceTonuonuoKaihu;
use Illuminate\Support\Facades\Mail;
use App\Models\Hotel\InvoiceRegister;
use Dcat\Admin\Widgets\Form as WidgetForm;

// 小程序直播
class LiveController extends AdminController
{

}
