<?php

namespace App\Admin\Controllers;

use App\Merchant\Repositories\Room;
use App\Models\Hotel\Room as RoomModel;
use App\Models\Hotel\WxappConfig;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use App\Models\Hotel\Hotel;
use App\Models\Hotel\HotelGoldPlan;
use Dcat\Admin\Widgets\Alert;
use App\Services\WxChatPayService;
use Dcat\Admin\Admin;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Dcat\Admin\Widgets\Modal;
use App\Admin\Forms\GoidplanUpverifyfile;
use App\Admin\Forms\UpdateXiaopiaobannerImg;

/**
 * 点金计划
 */
class GoldPlanController extends Controller
{

    public function index(Content $content)
    {
        //$this->config = WxappConfig::getConfig(143);
        return $content
            ->header('点金计划')
            ->description('全部')
            ->breadcrumb(['text' => '点金计划管理', 'uri' => ''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        //$user = Admin::guard()->user();
        $this->loadScript();
        $grid =  Grid::make(Hotel::with('goldplan'), function (Grid $grid) {
            $grid->model()->where([['id', '<>', 1], ['shop_open', '=', 1]])->orderBy('id', 'DESC');
            $grid->column('id')->sortable();
            //$grid->column('seller_id','商家');
            $grid->column('name', '酒店名');
            $grid->column('shop_open', '线上运营状态')->using([0 => '停止', 1 => '正常'])->label([
                0 => 'danger',
                1 => 'success',
            ]);
            $grid->column('goldplan.gold_plan_status', '点金计划状态')->using(['NULL' => '未操作', '0' => '关', '1' => '开'])->label([
                'default' => 'primary',
                0 => 'danger',
                1 => 'success',
            ]);
            $grid->column('goldplan.gold_plan_advertising_show', '广告展示')
                ->using(['NULL' => '未操作', '0' => '关', '1' => '开'])->label([
                    'default' => 'primary',
                    0 => 'danger',
                    1 => 'success',
                ]);
            $grid->column('goldplan.gold_plan_diy_xiaopiao', '是否自定义小票')
                ->using(['NULL' => '未操作', '0' => '关', '1' => '开'])->label([
                    'default' => 'primary',
                    0 => 'danger',
                    1 => 'success',
                ]);
            $grid->column('goldplan.verify_file', '验证文件')->display(function($verify_file){
                if(empty($verify_file)){
                    return "未上传";
                }
               return "已上传";
            });
            $grid->column('goldplan.xiaopiao_banner_img', 'banner图')->display(function($e){
                if(!empty($e)){
                    return "<img src='$e' style='width:100px;'/>";
                }
               return "<img src='/img/xiaop-banner1.jpg' style='width:100px;'/>";
            });
            /*$grid->actions(function (Grid\Displayers\Actions $actions) {
                $id = $actions->getKey();
                $actions->append("<a href='messages?conversation_id={$id}'><i></i>查看点金计划</a>");
            });*/
            $grid->actions(function ($actions) {
                $id = $actions->getKey();
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                $actions->disableView();
                $gold_plan_status =  !empty($actions->row->goldplan->gold_plan_status) ? $actions->row->goldplan->gold_plan_status : 0; //$actions->row->gold_plan_status;
                $gold_plan_advertising_show =  !empty($actions->row->goldplan->gold_plan_advertising_show) ? $actions->row->goldplan->gold_plan_advertising_show : 0; //$actions->row->gold_plan_advertising_show;
                $gold_plan_diy_xiaopiao = !empty($actions->row->goldplan->gold_plan_diy_xiaopiao) ? $actions->row->goldplan->gold_plan_diy_xiaopiao : 0;
                $hotel_name = $actions->row->name;
                if ($gold_plan_status === 1) {
                    $actions->append("<a href='javascript:void(0);' class='gold-plan-open tips' data-action_type='gold-plan-open'  data-name='$hotel_name' data-id='$id' data-status='0' data-title='关闭点金计划'> 关闭点金计划</a>");
                } else { 
                    $actions->append("<a href='javascript:void(0);' class='gold-plan-open tips' data-action_type='gold-plan-open'  data-name='$hotel_name' data-id='$id' data-status='1' data-title='开通点金计划'> 开通点金计划</a>");
                }

                if ($gold_plan_advertising_show === 1) {
                    $actions->append("<a href='javascript:void(0);' class='gold-plan-open'  data-action_type='gold_plan_advertising' data-name='$hotel_name' data-id='$id' data-status='0'> 广告关闭</a>");
                } else {
                    $actions->append("<a href='javascript:void(0);' class='gold-plan-open'  data-action_type='gold_plan_advertising' data-name='$hotel_name' data-id='$id' data-status='1'> 广告开启</a>");
                }

                if ($gold_plan_diy_xiaopiao === 1) {
                    $actions->append("<a href='javascript:void(0);' class='gold-plan-open'  data-action_type='gold_plan_diy_xiaopiao' data-name='$hotel_name' data-id='$id' data-status='0'> 自定义小票关闭</a>");
                } else {
                    $actions->append("<a href='javascript:void(0);' class='gold-plan-open'  data-action_type='gold_plan_diy_xiaopiao' data-name='$hotel_name' data-id='$id' data-status='1'> 自定义小票开启</a>");
                }
                $modal = Modal::make()
                ->lg()
                ->title('上传验证文件')
                ->body(GoidplanUpverifyfile::make()->payload($actions->row->toArray()))
                ->button('<span>上传验证文件</span>');
                $actions->append($modal);

                $modal = Modal::make()
                ->lg()
                ->title('更新商家小票banner图')
                ->body(UpdateXiaopiaobannerImg::make()->payload($actions->row->toArray()))
                ->button('<span>更新商家小票banner图</span>');
                $actions->append($modal);

                //$actions->disableView();
            });
            $grid->disableCreateButton();
            $grid->quickSearch(['name'])->placeholder('酒店id,酒店名称');
            //$grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('name');
            });
        });
        $xiaopiao_url = env('APP_URL').'/goldplan';
        $htmll = <<<HTML
<ul>
    <li>只有当用户自己扫码微信付款时(商家二维码收款)，支付成功后，会展示商家小票。微信付款码(当面付) 支付不支持展示。</li>
    <li>查看微信官方 <a href="https://pay.weixin.qq.com/docs/partner/products/gold-plan/introduction.html" target="_blank"> <b class="text-danger">点金计划</b></a> 详细介绍</li>
    <li>自定义商家小票说明：<a href="https://pay.weixin.qq.com/docs/partner/products/gold-plan/preparation.html" target="_blank"> <b class="text-danger">查看</b></a> 接入前准备</li>
    <li>平台提供的 自定义商家小票链接：{$xiaopiao_url} <a class="clipboard-txt" data-clipboard-text='{$xiaopiao_url}' href="javascript:void(0);" > <i class='feather icon-copy'></i></a></li>
    <li>默认的商家小票Banner图： <a  href="https://hotel.rongbaokeji.com//img/xiaop-banner1.jpg" target="_blank"> 查看</a>，每个商家可以自由更换</li>
    <li>查看如何下载验证文件：<a href="https://hotel.rongbaokeji.com/docs/showdoc/web/#/627898594/223959919" target="_blank"> 查看 </a></li>
</ul>
HTML;

        $alert = Alert::make($htmll, '提示:');
        return $alert->info() . $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new Hotel(), function (Show $show) {
            $show->field('id');
            $show->field('name');
        });
    }
    // 操作点击计划
    public function actions(Content $content)
    {
        $request = Request();
        $hotel_id = $request->get('hotel_id');
        $hotel_name = $request->get('hotel_name');
        $status = $request->get('status');
        $action_type = $request->get('action_type');
        $hotelinfo = HotelGoldPlan::where(['hotel_id' => $hotel_id])->first();
        //$wxconfig = WxappConfig::getConfig($hotel_id);
        //$msg = '';

        $isvpay  = app('wechat.isvpay');
        $config  = $isvpay->getOauthInfo('', $hotel_id);

        $instance = (new \App\libary\WeChatPay\WeChatPay())->makePay();
        $operation_type = '';
        $msg = '';
        if ($status == 1) {
            $gold_plan_status = 1;
            $operation_type = 'OPEN';
            $msg = '成功开启';
        }
        if ($status == 0) {
            $gold_plan_status = 0;
            $operation_type = 'CLOSE';
            $msg = '成功关闭';
        }
        if ($action_type == 'gold-plan-open') {
            $postdata = [
                'sub_mchid'           => $config->sub_mch_id,
                'operation_type'      => $operation_type,
                'operation_pay_scene' => 'JSAPI'
            ];
            try {
                $resp = $instance->chain('/v3/goldplan/merchants/changegoldplanstatus')->post([
                    'json' => $postdata
                ]);

                $res = $resp->getBody()->getContents();

            } catch (\Error $error) {

            } catch (\Exception $exception) {

            }

            addlogs('changegoldplanstatus', $postdata, $res);

            $resarr = json_decode($res, true);
            if (!empty($resarr['sub_mchid'])) {
                $hotelinfo->gold_plan_status = $gold_plan_status;
                $hotelinfo->save();
            } else {
                $msg = '操作遇到问题1';
            }
        }

        // 广告开启关闭
        if ($action_type == 'gold_plan_advertising') {

            if ($status == 1) {
                $advertising = 'open-advertising-show';
            } else {
                $advertising = 'close-advertising-show';
            }

            $apiurl = '/v3/goldplan/merchants/' . $advertising;
            $pdata = [
                'json' => [
                    'sub_mchid'           => $config->sub_mch_id,
                ]
            ];
            $resp = $instance->chain($apiurl)->post($pdata);
            $res = $resp->getBody()->getContents();

            addlogs($advertising, $pdata, $resp);
            $resarr = json_decode($res, true);
            if (!empty($resarr)) {
                $hotelinfo->gold_plan_advertising_show = $gold_plan_status;
                $hotelinfo->save();
            } else {
                $msg = '操作遇到问题2';
            }
        }

        // 自定义商家小票
        if ($action_type == 'gold_plan_diy_xiaopiao') {
            $apiurl = '/v3/goldplan/merchants/changecustompagestatus';
            $pdata = [
                'json' => [
                    'sub_mchid'           => $config->sub_mch_id,
                    'operation_type' => $operation_type
                ]
            ];
            $resp = $instance->chain($apiurl)->post($pdata);

            $res = $resp->getBody()->getContents();
            addlogs($apiurl, $pdata, $res);
            $resarr3 = json_decode($res, true);
            if (!empty($resarr3)) {
                $hotelinfo->gold_plan_diy_xiaopiao = $gold_plan_status;
                $hotelinfo->save();
            } else {
                $msg = '操作遇到问题3';
            }
        }

        return $content->full()->body(view('admin.full.gold-plan-open', compact('hotel_name', 'hotel_id', 'res', 'msg')));
    }

    public function loadScript()
    {
        Admin::script(
            <<<SCRIPT
    $('.gold-plan-open').click(function () {
        var id = $(this).attr('data-id');
        var hotel_name = $(this).attr('data-name');
        var status = $(this).attr('data-status');
        var action_type = $(this).attr('data-action_type');
        layer.open({
            type: 2,
            title: '操作微信点金计划',
            area: ['65%', '80%'],
            success: function(layero, index){
                $(layero).find('.layui-layer-close').click(function(){
                  window.location.reload();
                  layer.close(index);  // 关闭弹窗
                });
            },
            content: '/admin/goldplan/actions/?hotel_id=' + id + '&hotel_name=' + hotel_name + '&status=' + status + '&action_type='+action_type,
        });
    });
SCRIPT

        );
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Room(), function (Form $form) {
            $form->display('id');
            //$form->hidden('seller_id')->value(1);
            $form->text('name');
            $form->text('price');
            // 多图文上传
            /*->rules(function (Form $form) {
                return 'nullable|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif';
            })->accept('jpg,png,gif,jpeg,webp', 'image/*')->required();*/
            $form->image('logo')->disk('admin')->rules(function (Form $form) {
                return 'nullable|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif';
            })->accept('jpg,png,gif,jpeg,webp', 'image/*')->saveFullUrl()->autoUpload()->required();
            $form->multipleImage('moreimg')->disk('admin')->saveFullUrl()->autoUpload()->required();
            $form->text('floor');
            $form->text('people');
            $form->text('bed');
            $form->text('breakfast');
            $form->text('facilities');
            $form->text('windows');
            $form->text('total_num');
            $form->text('uniacid');
            $form->text('size');
            $form->text('is_refund');
            $form->text('yj_state');
            $form->text('yj_cost');
            $form->text('sort');
            $form->text('state');
            $form->text('classify');
            //$form->datetime('rz_time');
            $form->hidden('update_time')->default(time());
            $form->text('bed_num');
            $form->text('add_room');
            $form->text('pay_to_shop');
            $form->text('recommend');
            $form->text('area');
            $form->text('retail_price');
            $form->text('agreement_price');
            $form->text('member_price');
            $form->text('agreement_price_status');
            $form->text('member_price_status');
            $form->text('agreement_price_guide_status');
            $form->markdown('notes');
            $form->saving(function (Form $form) {
                $form->img = 1;
            });
        });
    }

    /**
     * 获取证书
     * @return mixed
     */
    public static function certificates()
    {
        //请求参数(报文主体)
        $headers = self::sign('GET', 'https://api.mch.weixin.qq.com/v3/certificates', '');
        $result  = self::curl_get('https://api.mch.weixin.qq.com/v3/certificates', $headers);
        $result  = json_decode($result, true);
        $aa      = self::decryptToString($result['data'][0]['encrypt_certificate']['associated_data'], $result['data'][0]['encrypt_certificate']['nonce'], $result['data'][0]['encrypt_certificate']['ciphertext']);
        dd($aa); //解密后的内容，就是证书内容
    }

    /**
     * 签名
     * @param string $http_method 请求方式GET|POST
     * @param string $url url
     * @param string $body 报文主体
     * @return array
     */
    public static function sign($http_method = 'POST', $url = '', $body = '')
    {
        $mch_private_key = self::getMchKey(); //私钥
        $timestamp       = time(); //时间戳
        $nonce           = self::getRandomStr(32); //随机串
        $url_parts       = parse_url($url);
        $canonical_url   = ($url_parts['path'] . (!empty($url_parts['query']) ? "?${url_parts['query']}" : ""));
        //构造签名串
        $message = $http_method . "\n" .
            $canonical_url . "\n" .
            $timestamp . "\n" .
            $nonce . "\n" .
            $body . "\n"; //报文主体
        //计算签名值
        openssl_sign($message, $raw_sign, $mch_private_key, 'sha256WithRSAEncryption');
        $sign = base64_encode($raw_sign);
        //设置HTTP头
        $config  = self::config();
        $token   = sprintf(
            'WECHATPAY2-SHA256-RSA2048 mchid="%s",nonce_str="%s",timestamp="%d",serial_no="%s",signature="%s"',
            $config['mchid'],
            $nonce,
            $timestamp,
            $config['serial_no'],
            $sign
        );
        $headers = [
            'Accept: application/json',
            'User-Agent: */*',
            'Content-Type: application/json; charset=utf-8',
            'Authorization: ' . $token,
        ];
        return $headers;
    }

    //私钥
    public static function getMchKey()
    {
        $config = config('wechat.min2');
        //path->私钥文件存放路径
        return openssl_get_privatekey(file_get_contents($config['key_path']));
    }

    /**
     * 获得随机字符串
     * @param $len      integer       需要的长度
     * @param $special  bool      是否需要特殊符号
     * @return string       返回随机字符串
     */
    public static function getRandomStr($len, $special = false)
    {
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );

        if ($special) {
            $chars = array_merge($chars, array(
                "!", "@", "#", "$", "?", "|", "{", "/", ":", ";",
                "%", "^", "&", "*", "(", ")", "-", "_", "[", "]",
                "}", "<", ">", "~", "+", "=", ",", "."
            ));
        }

        $charsLen = count($chars) - 1;
        shuffle($chars);                            //打乱数组顺序
        $str = '';
        for ($i = 0; $i < $len; $i++) {
            $str .= $chars[mt_rand(0, $charsLen)];    //随机取出一位
        }
        return $str;
    }

    /**
     * 配置
     */
    public static function config()
    {
        $config = config('wechat.min2');
        return [
            'appid'       => $config['app_id'],
            'mchid'       => $config['mch_id'], //商户号
            'serial_no'   => $config['serial_no'], //证书序列号
            'description' => '融宝科技', //应用名称（随意）
            'notify'      => $config['notify_url'], //支付回调
        ];
    }

    //get请求
    public static function curl_get($url, $headers = array())
    {
        $info = curl_init();
        curl_setopt($info, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($info, CURLOPT_HEADER, 0);
        curl_setopt($info, CURLOPT_NOBODY, 0);
        curl_setopt($info, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($info, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($info, CURLOPT_SSL_VERIFYHOST, false);
        //设置header头
        curl_setopt($info, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($info, CURLOPT_URL, $url);
        $output = curl_exec($info);
        curl_close($info);
        return $output;
    }

    const KEY_LENGTH_BYTE = 32;
    const AUTH_TAG_LENGTH_BYTE = 16;

    /**
     * Decrypt AEAD_AES_256_GCM ciphertext
     *
     * @param string $associatedData AES GCM additional authentication data
     * @param string $nonceStr AES GCM nonce
     * @param string $ciphertext AES GCM cipher text
     *
     * @return string|bool      Decrypted string on success or FALSE on failure
     */
    public static function decryptToString($associatedData, $nonceStr, $ciphertext)
    {
        $config     = config('wechat.min2');
        $aesKey     = $config['key'];
        $ciphertext = \base64_decode($ciphertext);
        if (strlen($ciphertext) <= self::AUTH_TAG_LENGTH_BYTE) {
            return false;
        }

        // ext-sodium (default installed on >= PHP 7.2)
        if (function_exists('\sodium_crypto_aead_aes256gcm_is_available') && \sodium_crypto_aead_aes256gcm_is_available()) {
            return \sodium_crypto_aead_aes256gcm_decrypt($ciphertext, $associatedData, $nonceStr, $aesKey);
        }

        // ext-libsodium (need install libsodium-php 1.x via pecl)
        if (function_exists('\Sodium\crypto_aead_aes256gcm_is_available') && \Sodium\crypto_aead_aes256gcm_is_available()) {
            return \Sodium\crypto_aead_aes256gcm_decrypt($ciphertext, $associatedData, $nonceStr, $aesKey);
        }

        // openssl (PHP >= 7.1 support AEAD)
        if (PHP_VERSION_ID >= 70100 && in_array('aes-256-gcm', \openssl_get_cipher_methods())) {
            $ctext   = substr($ciphertext, 0, -self::AUTH_TAG_LENGTH_BYTE);
            $authTag = substr($ciphertext, -self::AUTH_TAG_LENGTH_BYTE);

            return \openssl_decrypt(
                $ctext,
                'aes-256-gcm',
                $aesKey,
                \OPENSSL_RAW_DATA,
                $nonceStr,
                $authTag,
                $associatedData
            );
        }

        throw new \RuntimeException('AEAD_AES_256_GCM需要PHP 7.1以上或者安装libsodium-php');
    }
}
