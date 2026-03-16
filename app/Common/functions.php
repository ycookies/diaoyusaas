<?php
use App\Models\Hotel\Setting;
use EasyWeChat\Factory;

if (!function_exists('returnData')) {
    function returnData($code = '', $status = '', $data = [], $msg = '') {
        if ($status == 1) {
            $status = 'success';
        } else {
            $status = 'error';
        }
        if (!is_array($data)) {
            $status = 'error';
            //$msg = '给予返回的数据不是一个数组';
        } else {
            if (count($data) == 0) {
                $data = (object)array();
            }
        }
        $tipstr = config('errorCode.' . $code);
        if ($msg != '') {
            $tipstr = $msg;
        }
        return response()->json([
            'code'   => $code,
            'status' => $status,
            'msg'    => $tipstr,
            'ver'    => 'online',
            'data'   => $data,
        ]);
    }
}
if (!function_exists('apiReturn')) {
    function apiReturn($code = '', $status = '', $data = [], $msg = '') {
        if ($status == 1) {
            $status = 'SUCCESS';
            $code = 200;
        } else {
            $status = 'FAIL';
            $code = 204;
        }
        if (!is_array($data)) {
            $status = 'ERROR';
            //$msg = '给予返回的数据不是一个数组';
        } else {
            if (count($data) == 0) {
                $data = (object)array();
            }
        }
        $tipstr = config('errorCode.' . $code);
        if ($msg != '') {
            $tipstr = $msg;
        }
        header('Content-Type: application/json');
         die(json_encode([
             'ret'   => $code,
             //'status' => $status,
             'msg'    => $tipstr,
             'data'   => $data,
         ], JSON_UNESCAPED_UNICODE));
    }
}
if (!function_exists('returnData_j')) {
    function returnData_j($data = []) {
        return response()->json($data, JSON_UNESCAPED_UNICODE);
    }
}

if (!function_exists('returnData_x')) {
    function returnData_x($code = '', $status = '', $data = [], $msg = '', $xdata = []) {
        if ($status == 1) {
            $status = 'success';
        } else {
            $status = 'error';
        }
        if (!is_array($data)) {
            $status = 'error';
            //$msg = '给予返回的数据不是一个数组';
        } else {
            if (count($data) == 0) {
                $data = (object)array();
            }
        }
        $tipstr = config('errorCode.' . $code);
        if ($msg != '') {
            $tipstr = $msg;
        }
        return response()->json([
            'code'   => $code,
            'status' => $status,
            'msg'    => $tipstr,
            'ver'    => 'online',
            'data'   => $data,
            'x_data' => $xdata,
        ], JSON_UNESCAPED_UNICODE);
    }
}

if (!function_exists('fina_type')) {
    function fina_type($type) {
        $typedata = [
            'in'  => '收入',
            'out' => '支出',
        ];
        $txt      = "收入";
        if ($type == 'in' || $type == 'out') {
            $txt = $typedata[$type];
        }
        return $txt;
    }
}


if (!function_exists('getNode')) {
    function getNode($xml, $node) {
        $encoding = mb_detect_encoding($xml);
        $xml      = '<?xml version="1.0" encoding="' . $encoding . '"?>' . $xml;
        $dom      = new DOMDocument ("1.0", $encoding);
        $dom->loadXML($xml);
        $event_type = $dom->getElementsByTagName($node);

        return $event_type->item(0)->nodeValue;
    }
}
if (!function_exists('get_local_image')) {
    function get_local_image($image_path) {
        $image_path_parse = pathinfo($image_path);

        return base64_decode($image_path_parse['filename']);
    }
}
if (!function_exists('savePicture')) {
    function savePicture($path, $name, $data = null) {
        if (!file_exists($path)) {
            if (mkdir($path, 0777, true)) {
                chmod($path, 0777);
            } else {
                abort($path . '无权限创建目录');
            }
            if (!is_writable($path)) {
                abort($path . '无权限操作');
            }
        } else {
            if (!is_writeable($path)) {
                abort($path . '无权限操作');
            }
            file_put_contents($path . '/' . $name, $data, LOCK_EX);
        }
    }
}

if (!function_exists('object2array')) {
    function object2array($obj) {
        switch (gettype($obj)) {
            case'object':
            case 'array':
                $tmpArray = [];
                foreach ($obj as $key => $value) {
                    if (is_object($value) || is_array($value)) {
                        $tmpArray[$key] = object2array($value);
                    } else {
                        $tmpArray[$key] = $value;
                    }
                }
                return $tmpArray;
            case 'string':
            case 'integer':
            case 'boolean':
                return [$obj];
            default:
                return $obj;
        }
    }
}
if (!function_exists('write_log')) {
    function write_log() {
        if (func_num_args() <= 1) {
            $level = 'NOTIFY';
            $data  = print_r(func_get_args()[0], true);
        } else {
            $level = func_get_args()[0];
            $data  = print_r(func_get_args()[1], true);
        }
        file_put_contents(storage_path('logs/error_exception.log'), '[ ' . date('Y-m-d H:i:s', time()) . ' ] [' . $level . ']' . "\n" . $data . "\n\n", FILE_APPEND);
    }
}

if (!function_exists('HttpsCurl')) {
    function HttpsCurl($url, $data = null,$ispost = true) {
        try {
            if(!$ispost && !empty($data) && is_array($data)){
                if(strpos($url,'?') === false){
                    $url = $url.'?'.http_build_query($data);
                }else{
                    $url = $url.'&'.http_build_query($data);
                }
            }
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            if($ispost){
                if (!empty($data) && is_array($data)) {
                    $data = json_encode($data, JSON_UNESCAPED_UNICODE);
                    curl_setopt($curl, CURLOPT_POST, 1);
                    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=utf-8'));
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
            }
            if (!isset($_SERVER["SERVER_NAME"]) && empty($_SERVER["SERVER_NAME"])) {
                $appurl = env('APP_URL');
            } else {
                $appurl = $_SERVER["SERVER_NAME"];
            }
            curl_setopt($curl, CURLOPT_REFERER, 'http://' . $appurl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $output = curl_exec($curl);
            curl_close($curl);
            return $output;
        } catch (\Exception $exception) {
            info('Curl错误:' . $exception->getMessage() . ' . 行号:' . $exception->getLine());
        }
    }
}


if (!function_exists('DingRobot')) {
    //钉钉机器人
    function DingRobot($title, $text, $id) {
        $messageUrl = env('APP_URL')."/log/info?logid=" . $id;//$_SERVER['HTTP_HOST'];
        $data       = [
            'msgtype' => 'link',
            'link'    => [
                'text'       => '[旅忆行]' . $text,
                'title'      => $title,
                'picUrl'     => '',
                'messageUrl' => $messageUrl
            ]
        ];
        if (env('APP_URL') == 'https://hotel.saishiyun.net') {
            $url = 'https://oapi.dingtalk.com/robot/send?access_token=f29e9dc7fd86ea36ed70bafb299122a91c1212251af12f78bb20ef3848f6b933';
            //$url = 'https://oapi.dingtalk.com/robot/send?access_token=dcfe1d9321635b50b8daf104a4cef149e3f491263eeae7a947bed1f37fbdd328';
            HttpsCurl($url, $data);
        }
        return true;
    }
}

/**
 * 企业微信预警机器人
 * @param $title
 * @param $text
 * @param int $id
 * @return bool
 * author eRic
 * dateTime 2020-03-09 14:15
 */
function WxDingRobot($title, $text = '请及时查看', $id = 0)
{
    $messageUrl = env('APP_URL')."/log/info/?logid=" . $id;//$_SERVER['HTTP_HOST'];
    $data       = [
        'msgtype' => 'news',
        'news'    => [
            'articles' => [
                'title' => $title,
                'description' => $text,
                'url' => $messageUrl,
                'picurl' => '',
            ]
        ]
    ];
    $url        = 'https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=cbdb3916-8f06-4389-bf2c-4749e4820903';
    HttpsCurl($url, $data);
    return true;
}

/**
 * 企业微信 进件通知机器人
 * @param $title
 * @param $text
 * @param int $id
 * @return bool
 * author eRic
 * dateTime 2020-03-09 14:15
 */
function WxRobotDoc($title, $text = '请及时查看', $id = 0)
{
    $messageUrl = env('APP_URL')."/admin/material-collect/form?role=1&business_code=" . $id;//$_SERVER['HTTP_HOST'];
    $data       = [
        'msgtype' => 'news',
        'news'    => [
            'articles' => [
                'title' => $title,
                'description' => $text,
                'url' => $messageUrl,
                'picurl' => '',
            ]
        ]
    ];
    $url        = 'https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=1d950e87-8fcc-447f-bb63-f1d78eb26219';
    HttpsCurl($url, $data);
    return true;
}

/**
 * 企业微信 酒店小程序,公众号客服中心
 * @param $robot_url 群机器人地址
 * @param $type 类型,wxgzh,minapp,wxh5,pcweb
 * @param $title 标题
 * @param $text 内容
 * @param int $logid 客服日志id
 * @return bool
 * author eRic
 * dateTime 2020-03-09 14:15
 */
function WxRobotkefuCenter($robot_url,$title, $text = '请及时查看',$type ='wxgzh', $id = 0)
{
    $messageUrl = env('APP_URL')."/kefucenter/index?logid=" . $id;
    $picurl = '/img/kefu-'.$type.'-tips.png';
    if(!file_exists(public_path($picurl))){
        $picurl = '/img/kefu-zixun-tips.png';
    }
    $data       = [
        'msgtype' => 'news',
        'news'    => [
            'articles' => [
                'title' => $title,
                'description' => $text,
                'url' => $messageUrl,
                'picurl' => env('APP_URL').$picurl,
            ]
        ]
    ];
    HttpsCurl($robot_url, $data);
    return true;
}

/**
 * 企业微信 订房通知机器人
 * @param $title
 * @param $text
 * @param int $id
 * @return bool
 * author eRic
 * dateTime 2020-03-09 14:15
 */
function WxRobotBooking($title, $text = '请及时查看', $id = 0)
{
    $messageUrl = env('APP_URL')."/booking-order/view?order_no=" . $id;//$_SERVER['HTTP_HOST'];
    $data       = [
        'msgtype' => 'news',
        'news'    => [
            'articles' => [
                'title' => $title,
                'description' => $text,
                'url' => $messageUrl,
                'picurl' => env('APP_URL').'/img/order-tips.png',
            ]
        ]
    ];
    $url        = 'https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=ed2f2046-08be-49db-aaf7-878786715c35';
    HttpsCurl($url, $data);
    return true;
}
/**
 * 企业微信 订房系统异常
 * @param $title
 * @param $text
 * @param int $id
 * @return bool
 * author eRic
 * dateTime 2020-03-09 14:15
 */
function WxRobotError($title, $text = '请及时查看', $id = 0)
{
    $messageUrl = env('APP_URL')."/log/info?id=" . $id;//$_SERVER['HTTP_HOST'];
    $data       = [
        'msgtype' => 'news',
        'news'    => [
            'articles' => [
                'title' => $title,
                'description' => $text,
                'url' => $messageUrl,
                'picurl' => '',
            ]
        ]
    ];
    $url        = 'https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=967d5622-42fb-4a0e-abf7-11b76c8e202d';
    HttpsCurl($url, $data);
    return true;
}
//记录日志
if (!function_exists('addlogs')) {
    function addlogs($apiname, $requet, $result, $mid = 0, $url = '-') {

        if (is_array($requet)) {
            $requet = json_encode($requet,JSON_UNESCAPED_UNICODE);
        }
        if (is_object($requet)) {
            $requet = json_encode((array)$requet,JSON_UNESCAPED_UNICODE);
        }
        if (is_array($result)) {
            $result = json_encode($result,JSON_UNESCAPED_UNICODE);
        }
        if (is_object($result)) {
            $result = json_encode((array)$result,JSON_UNESCAPED_UNICODE);
        }
        if ($url == '-') {
            $url = Request::url();
        }
        $logdata = [
            'apiname'    => $apiname,
            'pageurl'    => $url,
            'request'    => $requet, //请求
            'result'     => $result, //响应
            'user_id'    => $mid,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $id      = DB::table('apilogs')->insertGetId($logdata);
        return $id;
    }
}

/**
 * 获取今天 本星期 上周 上月 上季度 开始时间 和 结束时间
 */
if (!function_exists('TimeReturn')) {
    function TimeReturn($type, $t_type) {
        switch ($type) {
            case 'today':
                if ($t_type == "start") {
                    return date('Y-m-d 00:00:00');
                } else {
                    return date('Y-m-d H:i:s');
                }
                break;
            case 'yesterday':
                if ($t_type == "start") {
                    return date('Y-m-d 00:00:00', strtotime('-1 day'));
                } else {
                    return date('Y-m-d 23:59:59', strtotime('-1 day'));
                }
                break;
            case 'thisweek':
                if ($t_type == 'start') {
                    return date('Y-m-d 00:00:00', strtotime('this week Monday'));
                } else {
                    return date('Y-m-d H:i:s', time());
                }
                break;
            case 'lastweek':
                if ($t_type == "start") {
                    return date('Y-m-d 00:00:00', strtotime('last week Monday'));
                } else {
                    return date('Y-m-d 23:59:59', strtotime('last week Sunday'));
                }
                break;
            case 'thismonth':
                if ($t_type == 'start') {
                    return date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), 1, date("Y")));
                } else {
                    return date("Y-m-d h:i:s", time());
                }
                break;
            case 'lastmonth':
                if ($t_type == 'start') {
                    return date("Y-m-d H:i:s", mktime(0, 0, 0, date("m") - 1, 1, date("Y")));
                } else {
                    return date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), 0, date("Y")));
                }
                break;
            case 'thisseason':
                $season = ceil((date('n')) / 3);
                if ($t_type == "start") {
                    return date('Y-m-d H:i:s', mktime(0, 0, 0, $season * 3 - 3 + 1, 1, date('Y')));
                } else {
                    return date('Y-m-d H:i:s', time());
                }
                break;
            case 'lastseason':
                $season = ceil((date('n')) / 3) - 1;
                if ($t_type == "start") {
                    return date('Y-m-d H:i:s', mktime(0, 0, 0, $season * 3 - 2, 1, date('Y')));
                } else {
                    return date('Y-m-d H:i:s', mktime(23, 59, 59, $season * 3, date('t', mktime(0, 0, 0, $season * 3, 1, date("Y"))), date('Y')));
                }
                break;
            case 'halfyear':
                $time = strtotime('-5 month', time());
                if ($t_type == "start") {
                    return date('Y-m-d 00:00:00', mktime(0, 0, 0, date('m', $time), 1, date('Y', $time)));
                } else {
                    return date('Y-m-d H:i:s', time());
                }
                break;
        }
    }
}

/**
 * 获取指定时间  当天开始  的时间
 * 时间戳或字符串
 */
if (!function_exists('getTimeBegin')) {
    function getTimeBegin($times) {
        if (is_int($times)) {
            $time = mktime(0, 0, 0, date('m', $times), date('d', $times), date('Y', $times));
            return date("Y-m-d H:i:s", $time);
        }

        $time = mktime(0, 0, 0, date('m', strtotime($times)), date('d', strtotime($times)), date('Y', strtotime($times)));

        return date("Y-m-d H:i:s", $time);
    }
}

/**
 * 获取指定时间  当天结束  的时间
 * 时间戳或字符串
 */
if (!function_exists('getTimeEnd')) {
    function getTimeEnd($times) {
        if (is_int($times)) {
            $time = mktime(23, 59, 59, date('m', $times), date('d', $times), date('Y', $times));
            return date("Y-m-d H:i:s", $time);
        }

        $time = mktime(23, 59, 59, date('m', strtotime($times)), date('d', strtotime($times)), date('Y', strtotime($times)));

        return date("Y-m-d H:i:s", $time);
    }
}
/**
 * 去除数组中的值的空格
 */
if (!function_exists('TrimArray')) {
    function TrimArray($arr) {
        if (!is_array($arr)) {
            return $arr;
        }
        while (list($key, $value) = each($arr)) {
            if (is_array($value)) {
                $arr[$key] = TrimArray($value);
            } else {
                $arr[$key] = trim($value);
            }
        }
        return $arr;
    }
}
if (!function_exists('isMobile')) {
    //验证手机号码
    function isMobile($string) {
        return !!preg_match('/^1[3|4|5|6|7|8|9]\d{9}$/', $string);
    }
}
/**
 * 在线预览PDF 不保存到本地
 * $pdf_base64  PDF文件 base64
 */
if (!function_exists('read_pdf')) {
    function read_pdf($pdf_base64) {
//Decode pdf content
        $data = base64_decode($pdf_base64);
        header('Content-Type: application/pdf');
        echo $data;
    }
}

/**
 * 生成随机字符串
 * @param int $length 要生成的随机字符串长度
 * @param string $type 随机码类型：0，数字+大写字母；1，数字；2，小写字母；3，大写字母；4，特殊字符；-1，数字+大小写字母+特殊字符
 * @return string
 */
if (!function_exists('randCode')) {
    function randCode($length = 6, $type = 0) {
        $arr  = array(1 => "0123456789", 2 => "abcdefghijklmnopqrstuvwxyz", 3 => "ABCDEFGHIJKLMNOPQRSTUVWXYZ");
        $code = "";
        if ($type == 0) {
            array_pop($arr);
            $string = implode("", $arr);
        } else if ($type == "-1") {
            $string = implode("", $arr);
        } else {
            $string = $arr[$type];
        }
        $count = strlen($string) - 1;
        for ($i = 0; $i < $length; $i++) {
            $str[$i] = $string[rand(0, $count)];
            $code    .= $str[$i];
        }
        return $code;
    }
}

/**
 * 判断是否是日期格式
 * @param string $date 日期
 * @return bool
 */
if (!function_exists('is_date')) {
    function isDate($date) {
        $is_date = strtotime($date) ? strtotime($date) : false;
        if ($is_date === false) {
            return false;
        } else {
            return true;
        }
    }
}

/**
 * 在线预览PDF 不保存到本地
 * $pdf_base64  PDF文件 base64
 */
if (!function_exists('read_pdf')) {
    function read_pdf($pdf_base64) {
        $data = base64_decode($pdf_base64);
        header('Content-Type: application/pdf');
        echo $data;
    }
}

if (!function_exists('createPoster')) {
    // 生成宣传海报
    function createPoster($config = array(), $filename = "") {
        //如果要看报什么错，可以先注释调这个header
        if (empty($filename)) header("content-type: image/png");
        $imageDefault = array(
            'left'    => 0,
            'top'     => 0,
            'right'   => 0,
            'bottom'  => 0,
            'width'   => 100,
            'height'  => 100,
            'opacity' => 100
        );
        $textDefault  = array(
            'text'      => '',
            'left'      => 0,
            'top'       => 0,
            'fontSize'  => 32,       //字号
            'fontColor' => '255,255,255', //字体颜色
            'angle'     => 0,
        );
        $background   = $config['background'];//海报最底层得背景
        //背景方法
        $backgroundInfo   = getimagesize($background);
        $backgroundFun    = 'imagecreatefrom' . image_type_to_extension($backgroundInfo[2], false);
        $background       = $backgroundFun($background);
        $backgroundWidth  = imagesx($background);  //背景宽度
        $backgroundHeight = imagesy($background);  //背景高度
        $imageRes         = imageCreatetruecolor($backgroundWidth, $backgroundHeight);
        $color            = imagecolorallocate($imageRes, 0, 0, 0);
        imagefill($imageRes, 0, 0, $color);
        // imageColorTransparent($imageRes, $color);  //颜色透明
        imagecopyresampled($imageRes, $background, 0, 0, 0, 0, imagesx($background), imagesy($background), imagesx($background), imagesy($background));
        //处理了图片
        if (!empty($config['image'])) {
            foreach ($config['image'] as $key => $val) {
                $val      = array_merge($imageDefault, $val);
                $info     = getimagesize($val['url']);
                $function = 'imagecreatefrom' . image_type_to_extension($info[2], false);
                if ($val['stream']) {   //如果传的是字符串图像流
                    $info     = getimagesizefromstring($val['url']);
                    $function = 'imagecreatefromstring';
                }
                $res       = $function($val['url']);
                $resWidth  = $info[0];
                $resHeight = $info[1];
                //建立画板 ，缩放图片至指定尺寸
                $canvas = imagecreatetruecolor($val['width'], $val['height']);
                imagefill($canvas, 0, 0, $color);
                //关键函数，参数（目标资源，源，目标资源的开始坐标x,y, 源资源的开始坐标x,y,目标资源的宽高w,h,源资源的宽高w,h）
                imagecopyresampled($canvas, $res, 0, 0, 0, 0, $val['width'], $val['height'], $resWidth, $resHeight);
                $val['left'] = $val['left'] < 0 ? $backgroundWidth - abs($val['left']) - $val['width'] : $val['left'];
                $val['top']  = $val['top'] < 0 ? $backgroundHeight - abs($val['top']) - $val['height'] : $val['top'];
                //放置图像
                imagecopymerge($imageRes, $canvas, $val['left'], $val['top'], $val['right'], $val['bottom'], $val['width'], $val['height'], $val['opacity']);//左，上，右，下，宽度，高度，透明度
            }
        }
        //处理文字
        if (!empty($config['text'])) {
            foreach ($config['text'] as $key => $val) {
                $val = array_merge($textDefault, $val);
                list($R, $G, $B) = explode(',', $val['fontColor']);
                $fontColor   = imagecolorallocate($imageRes, $R, $G, $B);
                $val['left'] = $val['left'] < 0 ? $backgroundWidth - abs($val['left']) : $val['left'];
                $val['top']  = $val['top'] < 0 ? $backgroundHeight - abs($val['top']) : $val['top'];
                imagettftext($imageRes, $val['fontSize'], $val['angle'], $val['left'], $val['top'], $fontColor, $val['fontPath'], $val['text']);
                if (!empty($val['isTitle'])) {
                    imagettftext($imageRes, $val['fontSize'], $val['angle'], ($val['left'] + 1), $val['top'], $fontColor, $val['fontPath'], $val['text']);
                }
            }
        }
        //生成图片
        if (!empty($filename)) {
            $res = imagejpeg($imageRes, $filename, 90); //保存到本地
            imagedestroy($imageRes);
            if (!$res) return false;
            return $filename;
        } else {
            imagejpeg($imageRes);     //在浏览器上显示
            imagedestroy($imageRes);
        }
    }
}

if (!function_exists('wxRobot')) {
    function wxRobot($title, $text, $messageUrl) {
        $url  = 'https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=df8d5bcc-5fd2-48e3-ba87-4e2ea1e71c91';
        $data = [
            '' => ''
        ];
        HttpsCurl($url, $data);
    }
}

if (!function_exists('addWatermark')) {
    // 生成宣传海报
    function addWatermark($path_img, $filename = "") {
        $fontPath = base_path() . '/assets/fonts/simhei.ttf';
        $config   = array(
            'text'       => array(
                array(
                    'text'      => '仅喜云案件使用',
                    'left'      => 30,
                    'top'       => 100,
                    'fontPath'  => $fontPath,     //字体文件captcha/font/Elephant.ttf
                    'fontSize'  => 16,             //字号
                    'fontColor' => '170,170,170',       //字体颜色
                    'angle'     => 45,
                ),
                array(
                    'text'      => '仅喜云案件使用',
                    'left'      => 30,
                    'top'       => 300,
                    'fontPath'  => $fontPath,     //字体文件captcha/font/Elephant.ttf
                    'fontSize'  => 16,             //字号
                    'fontColor' => '170,170,170',       //字体颜色
                    'angle'     => 45,
                ),
                array(
                    'text'      => '仅喜云案件使用',
                    'left'      => 30,
                    'top'       => 500,
                    'fontPath'  => $fontPath,     //字体文件captcha/font/Elephant.ttf
                    'fontSize'  => 16,             //字号
                    'fontColor' => '170,170,170',       //字体颜色
                    'angle'     => 45,
                ),
                array(
                    'text'      => '仅喜云案件使用',
                    'left'      => 30,
                    'top'       => 700,
                    'fontPath'  => $fontPath,     //字体文件captcha/font/Elephant.ttf
                    'fontSize'  => 16,             //字号
                    'fontColor' => '170,170,170',       //字体颜色
                    'angle'     => 45,
                ),
                array(
                    'text'      => '仅喜云案件使用',
                    'left'      => 30,
                    'top'       => 900,
                    'fontPath'  => $fontPath,     //字体文件captcha/font/Elephant.ttf
                    'fontSize'  => 16,             //字号
                    'fontColor' => '170,170,170',       //字体颜色
                    'angle'     => 45,
                ),
                array(
                    'text'      => '仅喜云案件使用',
                    'left'      => 30,
                    'top'       => 1100,
                    'fontPath'  => $fontPath,     //字体文件captcha/font/Elephant.ttf
                    'fontSize'  => 16,             //字号
                    'fontColor' => '170,170,170',       //字体颜色
                    'angle'     => 45,
                ),

                array(
                    'text'      => '仅喜云案件使用',
                    'left'      => 200,
                    'top'       => 100,
                    'fontPath'  => $fontPath,     //字体文件captcha/font/Elephant.ttf
                    'fontSize'  => 16,             //字号
                    'fontColor' => '170,170,170',       //字体颜色
                    'angle'     => 45,
                ),
                array(
                    'text'      => '仅喜云案件使用',
                    'left'      => 60,
                    'top'       => 300,
                    'fontPath'  => $fontPath,     //字体文件captcha/font/Elephant.ttf
                    'fontSize'  => 16,             //字号
                    'fontColor' => '170,170,170',       //字体颜色
                    'angle'     => 45,
                ),
                array(
                    'text'      => '仅喜云案件使用',
                    'left'      => 400,
                    'top'       => 300,
                    'fontPath'  => $fontPath,     //字体文件captcha/font/Elephant.ttf
                    'fontSize'  => 16,             //字号
                    'fontColor' => '170,170,170',       //字体颜色
                    'angle'     => 45,
                ),
                array(
                    'text'      => '仅喜云案件使用',
                    'left'      => 60,
                    'top'       => 700,
                    'fontPath'  => $fontPath,     //字体文件captcha/font/Elephant.ttf
                    'fontSize'  => 16,             //字号
                    'fontColor' => '170,170,170',       //字体颜色
                    'angle'     => 45,
                ),
                array(
                    'text'      => '仅喜云案件使用',
                    'left'      => 60,
                    'top'       => 900,
                    'fontPath'  => $fontPath,     //字体文件captcha/font/Elephant.ttf
                    'fontSize'  => 16,             //字号
                    'fontColor' => '170,170,170',       //字体颜色
                    'angle'     => 45,
                ),
                array(
                    'text'      => '仅喜云案件使用',
                    'left'      => 60,
                    'top'       => 1100,
                    'fontPath'  => $fontPath,     //字体文件captcha/font/Elephant.ttf
                    'fontSize'  => 16,             //字号
                    'fontColor' => '170,170,170',       //字体颜色
                    'angle'     => 45,
                ),
            ),
            'background' => $path_img          //背景图
        );
        $info     = getimagesize($path_img);
        echo "<pre>";
        print_r($info);
        echo "</pre>";
        exit;
        //如果要看报什么错，可以先注释调这个header
        if (empty($filename)) header("content-type: image/png");
        $imageDefault = array(
            'left'    => 0,
            'top'     => 0,
            'right'   => 0,
            'bottom'  => 0,
            'width'   => 100,
            'height'  => 100,
            'opacity' => 100
        );
        $textDefault  = array(
            'text'      => '',
            'left'      => 0,
            'top'       => 0,
            'fontSize'  => 32,       //字号
            'fontColor' => '255,255,255', //字体颜色
            'angle'     => 0,
        );
        $background   = $config['background'];//海报最底层得背景
        //背景方法
        $backgroundInfo   = getimagesize($background);
        $backgroundFun    = 'imagecreatefrom' . image_type_to_extension($backgroundInfo[2], false);
        $background       = $backgroundFun($background);
        $backgroundWidth  = imagesx($background);  //背景宽度
        $backgroundHeight = imagesy($background);  //背景高度
        $imageRes         = imageCreatetruecolor($backgroundWidth, $backgroundHeight);
        $color            = imagecolorallocate($imageRes, 0, 0, 0);
        imagefill($imageRes, 0, 0, $color);
        // imageColorTransparent($imageRes, $color);  //颜色透明
        imagecopyresampled($imageRes, $background, 0, 0, 0, 0, imagesx($background), imagesy($background), imagesx($background), imagesy($background));
        //处理了图片
        if (!empty($config['image'])) {
            foreach ($config['image'] as $key => $val) {
                $val      = array_merge($imageDefault, $val);
                $info     = getimagesize($val['url']);
                $function = 'imagecreatefrom' . image_type_to_extension($info[2], false);
                if ($val['stream']) {   //如果传的是字符串图像流
                    $info     = getimagesizefromstring($val['url']);
                    $function = 'imagecreatefromstring';
                }
                $res       = $function($val['url']);
                $resWidth  = $info[0];
                $resHeight = $info[1];
                //建立画板 ，缩放图片至指定尺寸
                $canvas = imagecreatetruecolor($val['width'], $val['height']);
                imagefill($canvas, 0, 0, $color);
                //关键函数，参数（目标资源，源，目标资源的开始坐标x,y, 源资源的开始坐标x,y,目标资源的宽高w,h,源资源的宽高w,h）
                imagecopyresampled($canvas, $res, 0, 0, 0, 0, $val['width'], $val['height'], $resWidth, $resHeight);
                $val['left'] = $val['left'] < 0 ? $backgroundWidth - abs($val['left']) - $val['width'] : $val['left'];
                $val['top']  = $val['top'] < 0 ? $backgroundHeight - abs($val['top']) - $val['height'] : $val['top'];
                //放置图像
                imagecopymerge($imageRes, $canvas, $val['left'], $val['top'], $val['right'], $val['bottom'], $val['width'], $val['height'], $val['opacity']);//左，上，右，下，宽度，高度，透明度
            }
        }
        //处理文字
        if (!empty($config['text'])) {
            foreach ($config['text'] as $key => $val) {
                $val = array_merge($textDefault, $val);
                list($R, $G, $B) = explode(',', $val['fontColor']);
                $fontColor   = imagecolorallocate($imageRes, $R, $G, $B);
                $val['left'] = $val['left'] < 0 ? $backgroundWidth - abs($val['left']) : $val['left'];
                $val['top']  = $val['top'] < 0 ? $backgroundHeight - abs($val['top']) : $val['top'];

                imagettftext($imageRes, $val['fontSize'], $val['angle'], $val['left'], $val['top'], $fontColor, $val['fontPath'], $val['text']);

                if (!empty($val['isTitle'])) {
                    imagettftext($imageRes, $val['fontSize'], $val['angle'], ($val['left'] + 1), $val['top'], $fontColor, $val['fontPath'], $val['text']);
                }
            }
        }
        //生成图片
        if (!empty($filename)) {
            $res = imagejpeg($imageRes, $filename, 90); //保存到本地
            imagedestroy($imageRes);
            if (!$res) return false;
            return $filename;
        } else {
            imagejpeg($imageRes);     //在浏览器上显示
            imagedestroy($imageRes);
        }
    }

}

/**
 * @desc 给案件材料打水印
 * @param $imgpath
 * @param bool $savefile
 * @param int $font_size
 * @return bool|string
 * author eRic
 * dateTime 2021-04-29 14:13
 */
function RunCaseWater($imgpath, $savefile = true, $font_size = 36) {
    $fileinfo   = pathinfo($imgpath);
    $file_name  = $fileinfo['basename'];
    $path       = $fileinfo['dirname'] . '/';
    $water_w    = 300;
    $water_h    = 300;
    $angle      = -45;
    $over_flag  = false;
    $water_text = '电商侠案管云';
    $font       = base_path() . '/assets/fonts/simhei.ttf';
    //检查文件和水印
    if ($file_name == "" || $water_text == "") return '文件名为空或者水印为空!';
    //检测是否安装GD库
    if (false == function_exists("gd_info")) return "系统没有安装GD库，不能给图片加水印.";
    //设置输入、输出图片路径名
    $in_img  = $path . $file_name;
    $out_img = $in_img;
    if (!$over_flag) {
        $arr_in_name = explode(".", $file_name);
        $out_img     = $path . $arr_in_name[0] . "_water_" . date('is') . "." . $arr_in_name[1];
    }
    //检测图片是否存在
    if (!file_exists($in_img)) return "图片不存在！";
    $info = getimagesize($in_img);
    //通过编号获取图像类型
    $type = image_type_to_extension($info[2], false);
    //在内存中创建和图像类型一样的图像
    $fun = "imagecreatefrom" . $type;
    //图片复制到内存
    $image = $fun($in_img);
    //设置字体颜色和透明度 alpha 0-127 透明度
    $color    = imagecolorallocatealpha($image, 245, 245, 245, 75);
    $x_length = $info[0];
    $y_length = $info[1];
    //铺满屏幕
    for ($x = 10; $x < $x_length; $x) {
        for ($y = 20; $y < $y_length; $y) {
            imagettftext($image, $font_size, $angle, $x, $y, $color, $font, $water_text);
            $y += $water_h;
        }
        $x += $water_w;
    }
    $filename = $out_img;
    //生成图片
    if ($savefile) {
        //unlink($filename);
        $res = imagejpeg($image, $filename, 90); //保存到本地
        imagedestroy($image);
        if (!$res) return false;
        return $filename;
    } else {
        //浏览器输出 保存图片的时候 需要去掉
        header("Content-type:" . $info['mime']);
        imagejpeg($image);     //在浏览器上显示
        imagedestroy($image);
    }

    /* //浏览器输出 保存图片的时候 需要去掉
     header("Content-type:" . $info['mime']);
     $fun = "image" . $type;
     $fun($image);
     //保存图片
     $fun($image, $out_img);
     //销毁图片
     imagedestroy($image);*/
}

/**
 * @desc 获取目录下的所有文件
 * @param $path
 * @param $files
 * author eRic
 * dateTime 2021-04-27 14:57
 */
if (!function_exists('searchDir')) {
    function searchDir($path, &$files) {
        if (is_dir($path)) {
            $opendir = opendir($path);
            while ($file = readdir($opendir)) {
                if ($file != '.' && $file != '..' && $file != '__MACOSX' && $file != '.DS_Store') {
                    searchDir($path . '/' . $file, $files);
                }
            }
            closedir($opendir);
        }
        if (!is_dir($path)) {
            $files[] = $path;
        }
    }
}

// 增加用户行为日志
if (!function_exists('addUserActionLogs')) {
    function addUserActionLogs($data) {
        $id = DB::table('admin_action_logs')->insertGetId($data);
        return $id;
    }
}
if (!function_exists('strToUtf8')) {
    /**
     * @desc 转换为utf8字符集
     * @param $str
     * @return bool|false|string|string[]|null
     * author eRic
     * dateTime 2021-06-07 15:15
     */
    function strToUtf8($str) {
        $encode = mb_detect_encoding($str, array("ASCII", 'UTF-8', "GB2312", "GBK", 'BIG5'));
        if ($encode == 'UTF-8') {
            return $str;
        } else {
            return mb_convert_encoding($str, 'UTF-8', $encode);
        }
    }
}


/**
 * @desc 解析获取平台类型
 * author eRic
 * dateTime 2021-06-25 09:46
 */
function getUrlToPlatform($url) {
    $pool        = array(
        8 => '//2.taobao.com', // 咸鱼
        9 => '.tmall.hk', // 天猫国际

        1  => '.taobao.', // 淘宝
        2  => '.tmall.', // 天猫
        3  => '.jd.', // 京东
        5  => '.1688.', // 1688
        6  => array(  // 拼多多
                      '.mobile.yangkeduo.com.',
                      'yangkeduo.'
        ),
        7  => '.aliexpress.', // 速卖通
        10 => '.suning.', // 苏宁易购
        11 => '.vip.', // 唯品会
        12 => '.17zwd.', // 一起做网店
        13 => '.163.', // 网易严选
        14 => '.aliexpress.', // 小红书
    );
    $platform_id = 0;
    foreach ($pool as $key => $val) {
        if (is_array($val)) {
            foreach ($val as $k => $v) {
                if (stripos($url, $v) != false) {
                    $platform_id = $key;
                    break 2;
                }
            }
        } else {
            if (stripos($url, $val) != false) {
                $platform_id = $key;
                break;
            }
        }
    }
    return $platform_id;
}

/**
 * @desc 解析链接信息
 * author eRic
 * dateTime 2021-06-24 17:56
 */
function getLinkInfo($link) {
    $platform_id   = getUrlToPlatform($link);
    $goodsLinkinfo = getGoodsUrlInfo($platform_id, $link);
    $link_info     = [
        'platform_id'      => $platform_id,
        'outid'            => !empty($goodsLinkinfo['outid']) ? $goodsLinkinfo['outid'] : '',
        'skuid'            => !empty($goodsLinkinfo['skuid']) ? $goodsLinkinfo['skuid'] : '',
        'shop_id'          => !empty($goodsLinkinfo['shop_id']) ? $goodsLinkinfo['shop_id'] : '',
        'goods_short_link' => !empty($goodsLinkinfo['goods_short_link']) ? $goodsLinkinfo['goods_short_link'] : '',
    ];
    return $link_info;
}

/**
 * @desc 解析获取商品链接属性
 * author eRic
 * dateTime 2021-06-25 09:49
 */
function getGoodsUrlInfo($platform, $item_url) {
    $linkattr         = [];
    $pid              = $platform;
    $shopid           = 0;
    $skuid            = 0;
    $goods_short_link = '--';//优化后的短链接
    if ($pid == 5) {
        // 1688  https://detail.1688.com/offer/595757824734.html
        preg_match("/offer\/([0-9]+)\.html/", $item_url, $param);
        $outid = !empty($param[1]) ? $param[1] : '';
        if (!empty($outid)) {
            $goods_short_link = 'https://detail.1688.com/offer/' . $outid . '.html';
        }
    } else if ($pid == 3) {
        preg_match("/jd\.com\/([0-9]+)\./", $item_url, $param);
        $outid = !empty($param[1]) ? $param[1] : '';
        if (!empty($outid)) {
            $goods_short_link = 'https://item.jd.com/' . $outid . '.html';
        }
    } else if ($pid == 6) {
        // 拼多多 http://yangkeduo.com/goods.html?goods_id=246029244292
        preg_match("/[\?|&]goods_id=([0-9]+)/", $item_url, $param);
        $outid = !empty($param[1]) ? $param[1] : '';
        if (!empty($outid)) {
            $goods_short_link = 'http://yangkeduo.com/goods.html?goods_id=' . $outid;
        }
    } elseif ($pid == 10) {
        //https://product.suning.com/0030000707/10550623817.html
        $burl = basename($item_url);
        list($outid,) = explode(".", $burl);
        preg_match("/suning\.com\/([0-9]+)\/([0-9]+)\./", $item_url, $param);
        $shopid = !empty($param[1]) ? $param[1] : '';
        $outid  = !empty($param[2]) ? $param[2] : '';
        if ($shopid != '' && !empty($outid)) {
            $goods_short_link = 'https://product.suning.com/' . $shopid . '/' . $outid . '.html';
        }
    } else if ($pid == 7) { // 速卖通
        // https://www.aliexpress.com/item/4000218318213.html
        preg_match("/item\/([0-9]+)\.html/", $item_url, $param);
        $outid = !empty($param[1]) ? $param[1] : '';
        if ($outid != '') {
            $goods_short_link = 'https://www.aliexpress.com/item/' . $outid . '.html';
        }
    } else if ($pid == 8) { // 闲鱼
        preg_match("/[\?|&]id=([0-9]+)/", $item_url, $param);
        $outid = !empty($param[1]) ? $param[1] : '';
        if ($outid != '') {
            $goods_short_link = 'https://2.taobao.com/item.htm?id=' . $outid;
        }
    } elseif ($pid == 12) { // 12
        preg_match("/[\?|&]GID=([0-9]+)/", $item_url, $param);
        $outid = !empty($param[1]) ? $param[1] : 0;
    } else {
        preg_match("/[\?|&]id=([0-9]+)/", $item_url, $param);
        $outid = !empty($param[1]) ? $param[1] : '';

        preg_match("/[\?|&]skuid=([0-9]+)/", $item_url, $param1);
        $skuid = !empty($param1[1]) ? $param1[1] : '';
        if (empty($skuid)) {
            preg_match("/[\?|&]skuId=([0-9]+)/", $item_url, $param1);
            $skuid = !empty($param1[1]) ? $param1[1] : '';
        }
        if ($pid == 1 && !empty($outid)) {
            $goods_short_link = 'https://item.taobao.com/item.htm?id=' . $outid;
        }
        if ($pid == 2 && !empty($outid)) {
            $skuidstr = '';
            if (!empty($skuid)) {
                $skuidstr = '&skuid=' . $skuid;
            }
            $goods_short_link = 'https://detail.tmall.com/item.htm?id=' . $outid . $skuidstr;
        }
    }
    $result = [
        "outid"            => $outid,
        "shopid"           => $shopid,
        'skuid'            => $skuid,
        'goods_short_link' => $goods_short_link,
    ];
    return $result;
    /*switch ($platform) {
        case 1:
            break;
        case 2:
            break;
        case 3:
            break;
        case 5:
            break;
        case 6:
            break;
    }*/
}

/**
 * @desc 获取店铺iD
 * author eRic
 * dateTime 2021-06-19 10:43
 */
function getShopid($platform, $shop_link, $shop_id, $cpi_id) {
    if (!empty($shop_id)) return $shop_id;
    $res = HttpsCurl($shop_link);
    switch ($platform) {
        case '淘宝':
            preg_match_all('/userid=([0-9]+)"/is', $res, $paytitle);
            if (empty($paytitle[1][0])) {
                unset($paytitle);
                preg_match_all('/userId=([0-9]+)"/is', $res, $paytitle);
            }
            if (!empty($paytitle[1][0])) {
                \DB::table('case_parties_involves')->where('id', $cpi_id)->update(['dfdant_shop_id' => $paytitle[1][0]]);
                return $paytitle[1][0];
            }
            break;
        case '京东':
            preg_match_all('/shopId = "([0-9]+)";/is', $res, $paytitle);
            if (!empty($paytitle[1][0])) {
                \DB::table('case_parties_involves')->where('id', $cpi_id)->update(['dfdant_shop_id' => $paytitle[1][0]]);
                return $paytitle[1][0];
            }
            break;
        case '1688':
            break;
        case '':
            break;

    }

    return false;
}

/**
 * @desc 通过Es 获取店铺ID
 * author eRic
 * dateTime 2021-06-29 11:47
 */
function getEsToShopId($platform_id, $shop_nick, $shop_name) {
    $Ess                        = new EsService('shop');
    $where['gt']['platform_id'] = $platform_id;
    //$where['gt']['seller_nick'] = $shop_nick;
    $where['must'][] = [
        'type'                 => 'match',
        'field'                => 'seller_nick',
        'value'                => $shop_nick,
        'operator'             => 'and',
        'minimum_should_match' => '100%'
    ];
    $res             = $Ess->esQuery($where, 1, 2, ['shop_id', 'shop_tid', 'seller_id'], true);
    // 有可能填错，使用shop_name 再查一次
    if (empty($res['list'][0]['shop_tid'])) {
        $where1['gt']['platform_id'] = $platform_id;
        //$where['gt']['seller_nick'] = $shop_nick;
        $where1['must'][] = [
            'type'                 => 'match',
            'field'                => 'seller_nick',
            'value'                => $shop_name,
            'operator'             => 'and',
            'minimum_should_match' => '100%'
        ];
        $res              = $Ess->esQuery($where1, 1, 2, ['shop_id', 'shop_tid', 'seller_id'], true);
    }
    $shop_id = '';
    if ($platform_id == 1 || $platform_id == 2) {
        $shop_id = !empty($res['list'][0]['seller_id']) ? $res['list'][0]['seller_id'] : false;
    }
    if ($platform_id == 3) {
        $shop_id = !empty($res['list'][0]['shop_tid']) ? $res['list'][0]['shop_tid'] : false;
    }
    if ($platform_id == 5) {
        $shop_id = !empty($res['list'][0]['shop_tid']) ? $res['list'][0]['shop_tid'] : false;
    }
    return $shop_id;
}

/**
 * @desc 获取表格数据
 * @param $file_path_name
 * @return array|void
 * author eRic
 * dateTime 2021-03-02 11:35
 * @throws \PHPExcel_Exception
 * @throws \PHPExcel_Reader_Exception
 */
function getXlsData($file_path_name) {
    $filePath = $file_path_name;
    //$PHPExcelk = new \PHPExcel();

    /*默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/
    $PHPReader = new \PHPExcel_Reader_Excel2007();
    if (!$PHPReader->canRead($filePath)) {
        $PHPReader = new \PHPExcel_Reader_Excel5();
        if (!$PHPReader->canRead($filePath)) {
            echo '此Excel文件不能正确读取!';
            return;
        }
    }
    $PHPExcel = $PHPReader->load($filePath);
    /*读取excel文件中的第一个工作表*/
    $currentSheet = $PHPExcel->getSheet(0);
    /*取得最大的列号*/
    $allColumn = $currentSheet->getHighestColumn();
    /*取得一共有多少行*/
    $allRow = $currentSheet->getHighestRow();
    $blist  = [];
    $list   = [];
    for ($currentRow = 1; $currentRow <= $allRow; $currentRow++) {
        /*从第A列开始输出*/
        for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
            $val                        = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65, $currentRow)->getValue();/*ord()将字符转为十进制数*/
            $blist[($currentRow - 1)][] = $val;
            if ($currentColumn == "C") {
                if ($val == "") {
                    //break;
                    //echo "姓名项,有地方没有名字.请检查!";
                    //exit;
                }
                $list[] = $val;
            }
        }
    }
    return $blist;
}

/**
 * @desc 验证添加的案子店铺信息没有重复
 * author eRic
 * dateTime 2021-07-08 16:32
 */
function checkCaseShopIsRepeat($client_id, $shop_nick_arr) {
    $weiwancheng = [];
    foreach ($shop_nick_arr as $key => $itemone) {
        $platform = trim($itemone['platform']);
        $nick = trim($itemone['nick']);
        $where   = [];
        $where[] = ['cpi.advo_client_id', '=', $client_id];
        $where[] = ['cpi.dfdant_platform', '=', $platform];
        $where[] = ['cpi.dfdant_nick', '=', $nick];
        //$where[] = ['cpi.status', '=', 1];
        //$where[] = ['case.case_sub_status', '<>', 22];// 不是已经处理完
        //$where[] = ['case.pool_status', '<>', 3]; //不是回收站
        /*$count1  = DB::table('court_cases AS case')
            ->leftJoin('case_parties_involves as cpi', 'cpi.court_case_id', '=', 'case.id')
            ->where($where)->count();*/
        $count1 = DB::table('case_parties_involves as cpi')->where($where)->count();

        $where2   = [];
        $where2[] = ['cpi.advo_client_id', '=', $client_id];
        $where2[] = ['cpi.dfdant_platform', '=', $platform];
        $where2[] = ['cpi.dfdant_shop_name', '=', $nick];
        //$where2[] = ['cpi.status', '=', 1];
        //$where2[] = ['case.case_sub_status', '<>', 22];// 不是已经处理完
        //$where2[] = ['case.pool_status', '<>', 3]; //不是回收站
        /*$count2   = DB::table('court_cases AS case')
            ->leftJoin('case_parties_involves as cpi', 'cpi.court_case_id', '=', 'case.id')
            ->where($where2)->count();*/
        $count2  = DB::table('case_parties_involves as cpi')->where($where2)->count();
        if ($count1) {
            $weiwancheng[] = $itemone['nick'];
        }
        if ($count2) {
            $weiwancheng[] = $itemone['nick'];
        }
    }
    return $weiwancheng;
}

/**
 * @desc 计算两个日期差几天
 * @param $Date_1 较大日期
 * @param $Date_2 较小日期
 * @return float|int
 * author eRic
 * dateTime 2021-07-16 17:08
 */
function count_days($Date_1, $Date_2) {
    $d1   = strtotime($Date_1);
    $d2   = strtotime($Date_2);
    $Days = round(($d1 - $d2) / (60 * 60 * 24));
    return ($Days + 1);
}

function jiexistr($str, $is_num = false, $type = 'none') {
    if (empty($str)) return '';
    if ($str == 'none') return $str;
    $str     = explode(',', $str);
    $new_str = [];
    foreach ($str as $keys => $valss) {

        //$str = "php如何将字 符串中322的字母数字Asf f45d和中文_分割？";
        //$array = preg_split("/([a-zA-Z0-9]+)/", $str, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $mc1 = preg_split("/([0-9.]+)/is", $valss, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        //preg_match_all("/([0-9.]+)/is",$valss,$mc1);

        foreach ($mc1 as $itemsss) {
            $new_str[] = '%' . $itemsss . '%';
        }

        /*if(!empty($mc1[0][0]) && $type == 'title'){
            $mm = str_replace($mc1[0][0],',',$valss);
            $mm = explode(',',$mm);
            foreach ($mm as $itemss){
                if(empty($itemss)) continue;
                $new_str[] = '%'.$itemss.'%';
            }
             //= '%'..'%';
        }else{
            $new_str[] = '%'.$valss.'%';
        }
        if(!empty($mc1[0])){

        }*/

    }
    return implode('||', $new_str);
}

// 获取上传图片记录id
function getUpfileID($upfilearr) {
    if (empty($upfilearr)) return false;
    $ids = [];
    foreach ($upfilearr as $key => $value) {
        $value = str_replace('//', '/', $value);
        $resid = DB::table('case_upfile_logs')->where([['original_path', 'like', '%' . $value . '%']])->value('id');
        if(!$resid){
            $resid = DB::table('case_upfile_logs')->where([['new_path', 'like', '%' . $value . '%']])->value('id');
        }
        if ($resid) {
            $ids[] = $resid;
        }
    }
    if (empty($ids)) {
        return false;
    }
    return $ids;
}

// 通过逗号相连的字符串，获取图片url数组列表
function getUpfileUrl($str) {
    if (empty($str)) return [];

    $rootpath = '/Users/yangg/Downloads/xdgit/crm';
    if (env('APP_URL') == 'https://service.dsxia.cn') {
        $rootpath = '/data/case';
    }
    if (env('APP_URL') == 'http://crm.saishiyun.net') {
        $rootpath = '/data/wwwroot/crm.saishiyun.net';
    }
    $imgurl  = [];
    $str_arr = explode(',', $str);
    $reslist = DB::table('case_upfile_logs')->whereIn('id', $str_arr)
        ->select('id', 'original_path','new_path')->get();
    if ($reslist) {
        foreach ($reslist as $item) {
            $imgurl[] = [
                'id'            => $item->id,
                'original_path' => str_replace($rootpath, '', $item->original_path),
                'new_path' => str_replace($rootpath, '', $item->new_path)
            ];
        }
    }
    return $imgurl;
}

function verifyShareSet($shareArr) {
    $newarr = [];
    if (empty($shareArr)) {
        returnData(201, 0, [], '请设置分成项!');
    }
    $compare_price = '';
    $compare_ratio = '';
    $sub_amount = 0;
    foreach ($shareArr as $key =>  $item) {
        $iteminfo = explode(',', $item);
        if (count($iteminfo) != 4) {
            returnData(201, 0, [], '客户分成设置的不完整');
        }
        if($key != 0 && $sub_amount != $iteminfo[2] ){
            returnData(201, 0, [], '分成项不管几项 减出金额必须一致');
        }
        if ($iteminfo[0] == '' || $iteminfo[1] == '' || $iteminfo[2] == '') {
            returnData(201, 0, [], '客户分成设置的不正确,都应该是大于0的数值');
        }
        if (!is_numeric($iteminfo[0]) || !is_numeric($iteminfo[1]) || !is_numeric($iteminfo[2])) {
            returnData(201, 0, [$iteminfo], '客户分成设置的不正确,都应该是数值');
        }
        if (strpos($iteminfo[0], '.') !== false || strpos($iteminfo[1], '.') !== false || strpos($iteminfo[2], '.') !== false) {
            returnData(201, 0, [$iteminfo], '客户分成设置的不正确,都应该是整数');
        }

        if ($iteminfo[0] >= $iteminfo[1]) {
            returnData(201, 0, [], '最小金额不能大于 最大金额');
        }
        if ($iteminfo[2] > $iteminfo[1]) {
            returnData(201, 0, [], '减出金额不能大于最大金额');
        }
        if (!empty($compare_price)) {
            if ($iteminfo[0] != $compare_price) {
                returnData(201, 0, [], '下层的最小金额应该等于上层的最大金额');
            }
        }
        if (!empty($compare_ratio)) {
            //$next_ratio = bcmul($iteminfo[3],0.01,2);
            if ($iteminfo[3] <= $compare_ratio) {
                returnData(201, 0, [], '下层的分成比例必须大于上层的比例');
            }
        }
        $sub_amount = $iteminfo[2];
        $compare_price = $iteminfo[1];
        $compare_ratio = $iteminfo[3];
        $newarr[]      = [
            'min' => $iteminfo[0],
            'max' => $iteminfo[1],
            'cost' => $iteminfo[2],
            'bit'     => bcmul($iteminfo[3],0.01,2),
        ];
    }
    return $newarr;
}

if(!function_exists('delfile')){
    // 删除文件
    function delfile($path){
        if(file_exists($path)){
            unlink($path);
            return true;
        }
        return false;
    }
}

if(!function_exists('filterStr')){
    /**
     * @desc 过滤字符
     * @param $str
     * author eRic
     * dateTime 2021-09-28 10:30
     */
    function filterStr($str){
        $str = trim($str);
        $str = str_replace('↵', '', $str);
        $str = str_replace('	', '', $str);
        $str = str_replace("\n", '', $str);
        $str = str_replace('\t\n', '', $str);
        return $str;
    }
}

if (!function_exists('delUrlParam')){
    function delUrlParam ($param_arr){
        $mk = Request::getQueryString();
        $ms = explode('&',$mk);
        foreach ($ms as $key => $valuds){
            foreach ($param_arr as $param_name){
                if(strpos($valuds,$param_name) !== false){
                    unset($ms[$key]);
                }
            }

        }
        return implode('&',$ms);
    }
}

if(!function_exists('getNetContents')){
    function getNetContents($url,$params = array(),$referer = "",$is_mobile = false,$proxy = false){

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//若给定url自动跳转到新的url,有了下面参数可自动获取新url内容：302跳转
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,30000);
        curl_setopt($ch,CURLOPT_TIMEOUT,30000);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
        if (!empty($params)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        curl_setopt($ch, CURLOPT_ENCODING, "gzip, deflate, sdch");
        if($is_mobile){
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 8_0 like Mac OS X) AppleWebKit/600.1.3 (KHTML, like Gecko) Version/8.0 Mobile/12A4345d Safari/600.1.4'));

        }else{
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:51.0) Gecko/20100101 Firefox/51.0');

        }

        $referer = $referer ? $referer : $url;
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        $file_content = curl_exec($ch);
        $error_code = curl_errno($ch);
        $curl_info = curl_getinfo($ch);

        if($error_code || (!$file_content && $curl_info['http_code']!=200)){
            $file_content = false;
        }
        return $file_content;
    }
}
if(!function_exists('getUrlFilename')){
    // 获取url文件名
    function getUrlFilename($url){
        return basename(parse_url($url)['path']);
    }
}

// 结案获取侠气奖励
if(!function_exists('getFdianpoint')){
    // 结案获取侠气奖励
    function getFdianpoint($price){
        $fdian_point_arr = config('cachedata.fdian_pint');
        $fdian_point = 0;
        foreach ($fdian_point_arr as $key => $items){
            if($price > $items['min'] && $price <= $items['max'] ){
                $fdian_point = $key;
            }
        }
        return $fdian_point;
    }
}
// 更新案件进度 侠气奖励
if(!function_exists('getRewardPoint')){
    function getRewardPoint($case_point,$case_status_id){
        $fdian_point_arr = config('cachedata.reward_pint');
        $zhanbi = !empty($fdian_point_arr[$case_status_id])?$fdian_point_arr[$case_status_id]:0;
        if(empty($zhanbi) || empty($case_point)){
          return false;
        }
        $fdian_point =  bcmul($case_point,($zhanbi * 0.01),0);
        return $fdian_point;
    }
}
// 总销售客来定案件等级
if(!function_exists('totalsaleToCaselevel')){
    function totalsaleToCaselevel($price){
        $case_level_info = config('cachedata.case_level_info');
        $level = 'E级';
        foreach ($case_level_info as $key => $items){
            if($price > $items['min'] && $price <= $items['max'] ){
                $level = $key;
            }
        }
        return $level;
    }
}
if (!function_exists('getNumberTxt')) {
    function getNumberTxt($sum, $w = 2) {
        if ($sum >= 100000000) {
            return round($sum / 100000000, $w) . '亿';
        } else {
            if ($sum >= 10000) {
                return round($sum / 10000, $w) . "万";
            } else {
                return $sum;
            }
        }
    }
}
if (!function_exists('getPageRandNum')) {
    /**
     * @desc 获取分页随机数
     * @param $pagesize 每页记录数
     * @param int $total 总记录数
     * @return string
     * author eRic
     * dateTime 2021-11-02 11:47
     */
    function getPageRandNum($pagesize, $counts) {
        $totalPages = ceil($counts /$pagesize);
        $pagema = range(1,$totalPages);
        $xianzaipage = $pagema[array_rand($pagema)];
        // 如果已看页码存在，就排除这些

        session_start();
        $view_page = !empty($_SESSION['view_page']) ? $_SESSION['view_page']:[];//$request->session()->get('view_page');
        if(!empty($view_page)){
            $diff_arr = array_diff($pagema,$view_page);
            if(!empty($diff_arr)){
                $xianzaipage = $diff_arr[array_rand($diff_arr)];
            }else{
                $view_page = [];
                unset($_SESSION['view_page']);
            }
        }
        $view_page[] = $xianzaipage;
        $_SESSION['view_page'] = $view_page;
        return $xianzaipage;
    }
}
/**
 * @desc 检查是不是手机号或座机号
 * @param $str
 * author eRic
 * dateTime 2021-11-12 17:45
 */
function is_tels_phone($str){
    if(strpos($str,'-') !== 0){

    }else{

    }
}
// 是否有效的json
function is_json($string) {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}

// 过滤空值,但保留0和false
function filterNull($val){
    return ($val === '' || $val === null) ? false : true;
}

// 分成计算
function lawyer_money_old($params,$fenbit){
    //$params：参数
    //  $price:赔偿金额 $bit:律师服务金额比例 $client_server_bit:合作方佣金比例  $lawyer_intro_bit:律所推荐人佣金比例

    $params_str = $params;
    $price = !empty($fenbit['price']) ? $fenbit['price'] : 0;
    $bit = !empty($fenbit['bit']) ? $fenbit['bit'] : 0;
    $client_server_bit = !empty($fenbit['client_server_bit']) ? $fenbit['client_server_bit'] : 0;
    $min_money = !empty($fenbit['min_money']) ? $fenbit['min_money'] : 0;
    $lawyer_intro_bit = !empty($fenbit['lawyer_intro_bit']) ? $fenbit['lawyer_intro_bit'] : 0;
    $cost = !empty($fenbit['cost']) ? $fenbit['cost'] : 0;
    $reward = !empty($fenbit['reward']) ? $fenbit['reward'] : 0;

    $client_server_bit = $client_server_bit > 0.5 ? 0.5 : $client_server_bit;
    $lawyer_intro_bit = $lawyer_intro_bit > 0.1 ? 0.1 : $lawyer_intro_bit;
    if(!is_array($params)){
        $string = explode(";",$params);
        $bit = $bit ? $bit : $string[0];
        unset($string[0]);
        $params = [];
        foreach ($string as $value){
            $value = explode(',',$value);
            if(count($value) == 4){
                $params[] = [
                    'min'=>$value[0],
                    'max'=>$value[1],
                    'cost'=>$value[2],
                    'bit'=>floatval($value[3]),
                ];
            }
        }
    }
    $message = [];
    $message[] = '赔偿金额减除'.$params[0]['cost'].'后得到赔偿收益金额';
    foreach($params as $key => $val){
        $key_arrays[]=$val['min'];
        $message[] = '第'.($key+1).'步：剩余赔偿收益金额在'.$val['min'].'至'.$val['max'].'之间时按最高的部分乘以'.(($val['bit'])*100).'%';
    }
    $params = array_values($params);
    array_multisort($key_arrays,SORT_ASC,SORT_NUMERIC,$params);

    $index = -1;
    $rprice = $price - $params[0]['cost'];
    foreach ($params as $key => $value){
        if($rprice > $value['min']){
            $index = $key;
        }
    }
    $my = $client = 0;
    switch ($index){
        case 0:
            if($price > $params[0]['cost'] && $params[0]['bit'] > 0.0001){
                $my = ($price - $params[0]['cost']) * (1 - $params[0]['bit']) + $params[0]['cost'];
                $client = ($price - $params[0]['cost']) * ($params[0]['bit']);
            }else{
                $client = 0;
                $my = $price;
            }
            break;
        case 1:
            $my = (($params[0]['max']) * (1 - $params[0]['bit'])) +
                (($rprice - $params[0]['max']) * (1 - $params[1]['bit'])) + $params[0]['cost'];

            $client = (($params[0]['max']) * ($params[0]['bit'])) +
                ($rprice - $params[0]['max']) * ($params[1]['bit']);
            break;
        case 2:

            $my = (($params[0]['max']) * (1 - $params[0]['bit'])) +
                ($params[1]['max']) * (1 - $params[1]['bit']) +
                ($rprice - $params[0]['max'] - $params[1]['max']) * (1 - $params[2]['bit']) +
                $params[0]['cost'];

            $client = (($params[0]['max']) * ($params[0]['bit'])) +
                ($params[1]['max']) * ($params[1]['bit']) +
                ($rprice - $params[0]['max'] - $params[1]['max']) * ($params[2]['bit']) ;
            break;
        case 3:
            $my = (($params[0]['max']) * (1 - $params[0]['bit'])) +
                ($params[1]['max']) * (1 - $params[1]['bit']) +
                ($params[2]['max']) * (1 - $params[2]['bit']) +
                ($rprice - $params[0]['max'] - $params[1]['max'] - $params[2]['max']) * (1 - $params[3]['bit']) +
                $params[0]['cost'];

            $client = (($params[0]['max']) * ($params[0]['bit'])) +
                ($params[1]['max']) * ($params[1]['bit']) +
                ($params[2]['max']) * ($params[2]['bit']) +
                ($rprice - $params[0]['max'] - $params[1]['max'] - $params[2]['max']) * ($params[3]['bit']) ;
            break;
        case 4:
            $my = (($params[0]['max']) * (1 - $params[0]['bit'])) +
                ($params[1]['max']) * (1 - $params[1]['bit']) +
                ($params[2]['max']) * (1 - $params[2]['bit']) +
                ($params[3]['max']) * (1 - $params[3]['bit']) +
                ($rprice - $params[0]['max'] - $params[1]['max'] - $params[2]['max'] - $params[3]['max']) * (1 - $params[4]['bit']) +
                $params[0]['cost'];

            $client = (($params[0]['max']) * ($params[0]['bit'])) +
                ($params[1]['max']) * ($params[1]['bit']) +
                ($params[2]['max']) * ($params[2]['bit']) +
                ($params[3]['max']) * ($params[3]['bit']) +
                ($rprice - $params[0]['max'] - $params[1]['max'] - $params[2]['max'] - $params[3]['max']) * ($params[4]['bit']) ;
            break;
        case -1:
            $my = $price;
            $client = 0;
            break;
    }

    //=(E2-C2)*(1-D2)+C2
    //=B2*(1-D2)+(E3-B2-C2)*(1-D3)+C2
    //=B2*(1-D2)+(B3)*(1-D3)+((E4-B3-B2-C2)*(1-D4))+C2
    //=B2*(1-D2)+(B3)*(1-D3)+(B4)*(1-D4)+((E5-E4-B3-B2-C2)*(1-D5))+C2
    $price_string[] = $bit;
    foreach ($params as $value){
        $price_string[] = implode(",",$value);
    }
    $client = [
        'rule'=>$index,
        'price'=>$price,
        'my'=>($my - $cost), //先扣除成本
        'client'=>$client
    ];

    $my = $mytemp = round($client['my'] * (1-$bit),2);

    $lawyer = round($client['my'] * ($bit),2);
    $server = 0;
    if($client_server_bit){
        $server = $my * round($client_server_bit,2);
    }
    $my = round(($client['my'] - $server) * (1-$bit-$lawyer_intro_bit) - $reward,2);
    $lawyer = round(($client['my'] - $server) * ($bit) + $cost + $reward,2); //律所加上额外成本
    $lawyer_intro = round(($client['my'] - $server) * ($lawyer_intro_bit),2);

    $result = [
        'rule'=>$client['rule'],
        'price'=>$price,
        'my'=>$my,
        'my_bit'=>round($my/$price,2),
        'my_msg'=>'减除品牌方的分成'.($client['client'] + $server).'元 后乘以'.((1-$bit-$lawyer_intro_bit)*100).'%,等于'.$my.'元',
        'lawyer'=>$lawyer,
        'lawyer_bit'=>round($lawyer/$price,2),
        'lawyer_msg'=>'减除品牌方的分成'.($client['client']+$server).'元 后乘以'.($bit*100).'%,等于'.$lawyer.'元',
        'client'=>$client['client'],
        'client_bit'=>round($client['client']/$price,2),
        'client_msg'=>$message,
        'params'=>implode(";",$price_string),
        'cost'=>$cost,
        'reward'=>$reward

    ];
    if(true){
        $result['client_server'] = $server;
        $result['client_server_bit'] = round($result['client_server']/$price,2);
        $result['client_server_msg'] = '减除品牌方的分成'.$client['client'].'元 后乘以'.((1-$bit)*100).'%,等于'.$mytemp.'元,再乘以'.(($client_server_bit)*100).'%等于'.$result['client_server'].'元';
    }
    if(true){
        $result['lawyer_intro'] = $lawyer_intro;
        $result['lawyer_intro_bit'] = round($result['lawyer_intro']/$price,2);
        $result['lawyer_intro_msg'] = '减除品牌方的分成'.($client['client']+$server).'元 后乘以'.($lawyer_intro_bit*100).'%,等于'.$result['lawyer_intro'].'元';
    }


    $result['total'] = $result['client'] + $result['my'] + $result['lawyer'] + $result['client_server'] + $result['lawyer_intro'];
    $result['total_bit'] = round($result['my_bit'] + $result['lawyer_bit'] + $result['client_bit'] + $result['lawyer_intro_bit'] + $result['client_server_bit'],1);
    if($result['total_bit'] != 1 || $result['total'] != $price){
        $result['error'] = '计算规则公式有问题，请修正';
    }
    $lawyer_money = [
        'bit_money'=>$result['lawyer'] * 0.3,
        'chengben_money'=>$result['lawyer'] * 0.45,
        'fuwu_money'=>$result['lawyer'] * 0.25,
        'dsxia_money'=>0,
    ];
    if($min_money > 1000 && $min_money < $price){
        $fenbit_temp = $fenbit;
        $fenbit_temp['price'] = $min_money;
        $min_result = lawyer_money($params_str,$fenbit_temp);
        $lawyer_money = [
            'bit_money'=> bcmul($min_result['lawyer'],0.3,2), //$min_result['lawyer'] * 0.3, //分成比例金额
            'chengben_money'=>bcmul($min_result['lawyer'],0.45,2) ,//$min_result['lawyer'] * 0.45, //案件成本金额
            'fuwu_money'=>bcmul($min_result['lawyer'],0.25,2), //$min_result['lawyer'] * 0.25, // 律师服务费
            'dsxia_money'=> bcsub($result['lawyer'],$min_result['lawyer'],2), //$result['lawyer'] - $min_result['lawyer'], //平台额外奖励金额
        ];
    }
    $lawyer_money['total_money'] = $lawyer_money['bit_money'] + $lawyer_money['chengben_money'] + $lawyer_money['fuwu_money'] + $lawyer_money['dsxia_money'];
    $lawyer_money['test_money'] = $min_money ? round($min_money*1.2,0) : 10000;
    $lawyer_money['reward_money'] = $reward; //奖励金额

    $result['lawyer_view'] = $lawyer_money;

    if($min_money > 30000){
        $result['proposal'] = '建议：采用购买公证+律师函+和解的方式进行,和解不成可以考虑【直接立案诉讼】';
    }else if($min_money > 10000){
        $result['proposal'] = '建议：采用时间戳取证+律师函+和解的方式进行,和解不成【可以考虑是否立案诉讼】';
    }else{
        $result['proposal'] = '建议：采用时间戳取证+律师函+和解+诉前调解+庭前调解的方式进行';
    }

    return $result;
}

// 新的分成计算
function lawyer_money($params,$fenbit){
    //$params：参数
    //  $price:赔偿金额 $bit:律师服务金额比例 $client_server_bit:合作方佣金比例  $lawyer_intro_bit:律所推荐人佣金比例

    $params_str = $params;
    $price = !empty($fenbit['price']) ? $fenbit['price'] : 0;
    $bit = !empty($fenbit['bit']) ? $fenbit['bit'] : 0;
    $client_server_bit = !empty($fenbit['client_server_bit']) ? $fenbit['client_server_bit'] : 0;
    $min_money = !empty($fenbit['min_money']) ? $fenbit['min_money'] : 0;
    $lawyer_intro_bit = !empty($fenbit['lawyer_intro_bit']) ? $fenbit['lawyer_intro_bit'] : 0;
    $cost = !empty($fenbit['cost']) ? $fenbit['cost'] : 0;
    $reward = !empty($fenbit['reward']) ? $fenbit['reward'] : 0;

    $client_server_bit = $client_server_bit > 0.5 ? 0.5 : $client_server_bit;
    $lawyer_intro_bit = $lawyer_intro_bit > 0.1 ? 0.1 : $lawyer_intro_bit;
    if(!is_array($params)){
        $string = explode(";",$params);
        $bit = $bit ? $bit : $string[0];
        unset($string[0]);
        $params = [];
        foreach ($string as $value){
            $value = explode(',',$value);
            if(count($value) == 4){
                $params[] = [
                    'min'=>$value[0],
                    'max'=>$value[1],
                    'cost'=>$value[2],
                    'bit'=>floatval($value[3]),
                ];
            }
        }
    }
    $message = [];
    $message[] = '赔偿金额减除'.$params[0]['cost'].'后得到赔偿收益金额';
    foreach($params as $key => $val){
        $key_arrays[]=$val['min'];
        $message[] = '第'.($key+1).'步：剩余赔偿收益金额在'.$val['min'].'至'.$val['max'].'之间时按最高的部分乘以'.(($val['bit'])*100).'%';
    }
    $params = array_values($params);
    array_multisort($key_arrays,SORT_ASC,SORT_NUMERIC,$params);

    $index = -1;
    $rprice = $price - $params[0]['cost'];
    foreach ($params as $key => $value){
        if($rprice > $value['min']){
            $index = $key;
        }
    }
    $my = $client = 0;
    switch ($index){
        case 0:
            if($price > $params[0]['cost'] && $params[0]['bit'] > 0.0001){
                $my = ($price - $params[0]['cost']) * (1 - $params[0]['bit']) + $params[0]['cost'];
                $client = ($price - $params[0]['cost']) * ($params[0]['bit']);
            }else{
                $client = 0;
                $my = $price;
            }
            break;
        case 1:
            $my = (($params[0]['max']) * (1 - $params[0]['bit'])) +
                (($rprice - $params[0]['max']) * (1 - $params[1]['bit'])) + $params[0]['cost'];

            $client = (($params[0]['max']) * ($params[0]['bit'])) +
                ($rprice - $params[0]['max']) * ($params[1]['bit']);
            break;
        case 2:

            $my = (($params[0]['max']) * (1 - $params[0]['bit'])) +
                ($params[1]['max']) * (1 - $params[1]['bit']) +
                ($rprice - $params[0]['max'] - $params[1]['max']) * (1 - $params[2]['bit']) +
                $params[0]['cost'];

            $client = (($params[0]['max']) * ($params[0]['bit'])) +
                ($params[1]['max']) * ($params[1]['bit']) +
                ($rprice - $params[0]['max'] - $params[1]['max']) * ($params[2]['bit']) ;
            break;
        case 3:
            $my = (($params[0]['max']) * (1 - $params[0]['bit'])) +
                ($params[1]['max']) * (1 - $params[1]['bit']) +
                ($params[2]['max']) * (1 - $params[2]['bit']) +
                ($rprice - $params[0]['max'] - $params[1]['max'] - $params[2]['max']) * (1 - $params[3]['bit']) +
                $params[0]['cost'];

            $client = (($params[0]['max']) * ($params[0]['bit'])) +
                ($params[1]['max']) * ($params[1]['bit']) +
                ($params[2]['max']) * ($params[2]['bit']) +
                ($rprice - $params[0]['max'] - $params[1]['max'] - $params[2]['max']) * ($params[3]['bit']) ;
            break;
        case 4:
            $my = (($params[0]['max']) * (1 - $params[0]['bit'])) +
                ($params[1]['max']) * (1 - $params[1]['bit']) +
                ($params[2]['max']) * (1 - $params[2]['bit']) +
                ($params[3]['max']) * (1 - $params[3]['bit']) +
                ($rprice - $params[0]['max'] - $params[1]['max'] - $params[2]['max'] - $params[3]['max']) * (1 - $params[4]['bit']) +
                $params[0]['cost'];

            $client = (($params[0]['max']) * ($params[0]['bit'])) +
                ($params[1]['max']) * ($params[1]['bit']) +
                ($params[2]['max']) * ($params[2]['bit']) +
                ($params[3]['max']) * ($params[3]['bit']) +
                ($rprice - $params[0]['max'] - $params[1]['max'] - $params[2]['max'] - $params[3]['max']) * ($params[4]['bit']) ;
            break;
        case -1:
            $my = $price;
            $client = 0;
            break;
    }

    //=(E2-C2)*(1-D2)+C2
    //=B2*(1-D2)+(E3-B2-C2)*(1-D3)+C2
    //=B2*(1-D2)+(B3)*(1-D3)+((E4-B3-B2-C2)*(1-D4))+C2
    //=B2*(1-D2)+(B3)*(1-D3)+(B4)*(1-D4)+((E5-E4-B3-B2-C2)*(1-D5))+C2
    $price_string[] = $bit;
    foreach ($params as $value){
        $price_string[] = implode(",",$value);
    }
    $client = [
        'rule'=>$index,
        'price'=>$price,
        'my'=>$my, //先扣除成本
        'client'=>$client
    ];

    //计算奖励和成本的总金额
    $costs = $cost + $reward;
    //计算平台初步获得金额
    $my = $client['my'] - $costs;
    //计算品牌介绍人获的金额
    $server = 0;
    $mytemp = $client['my']*(1-$bit);
    if($client_server_bit){
        $server = ($mytemp - $cost) * round($client_server_bit,2);
    }
    //计算律所获益的金额
    $lawyer = round(($client['my'] - $costs - $server) * ($bit) + $costs,2);
    //计算平台最终获的金额
    $my = $my - ($lawyer + $server);

    //计算律所介绍人获的金额
    $lawyer_intro_bit = $lawyer_intro_bit ? round($lawyer_intro_bit/0.4,2) : 0;
    $lawyer_intro = $my > 1 ? round(($my) * $lawyer_intro_bit,2) : 0;
    //$lawyer_intro = $my+$costs > 1 ? round(($my+$costs) * $lawyer_intro_bit,2) : 0;
    //计算平台实际收益的金额
    $my = $my - $lawyer_intro+ $costs;

    $client_money = $client['client'];
    $result = [
        'rule'=>$client['rule'],
        'price'=>$price,
        'my'=>$my,
        'my_bit'=>round($my/$price ,2),
        'my_msg'=>'减除品牌方的分成'.($client_money + $server).'元 后乘以'.((1-$bit)*100).'%,后乘以'.($lawyer_intro_bit*100).'%等于'.$my.'元',
        'lawyer'=>$lawyer,
        'lawyer_bit'=>round($lawyer/$price,2),
        'lawyer_msg'=>'减除品牌方的分成'.($client_money+$server).'元 后乘以'.($bit*100).'%,等于'.$lawyer.'元',
        'client'=>$client_money,
        'client_bit'=>round($client_money/$price,2),
        'client_msg'=>$message,
        'params'=>implode(";",$price_string),
        'cost'=>$cost,
        'reward'=>$reward

    ];
    $lawyer_msg = '减除品牌方的分成'.($client_money+$server).'元';
    if($cost){
        $lawyer_msg .= ' 再减除成本'.$cost.'元';
    }
    if($cost){
        $lawyer_msg .= ' 再减除奖励'.$reward.'元';
    }
    $lawyer_msg .= '后乘以'.($bit*100).'%';
    if($cost){
        $lawyer_msg .= '再加上案件成本'.$cost.'元';
    }
    if($reward){
        $lawyer_msg .= '再加上个案奖励'.$reward.'元';
    }
    $lawyer_msg .= '等于'.$lawyer.'元';
    $result['lawyer_msg'] = $lawyer_msg;
    if(true){
        $result['client_server'] = $server;
        $result['client_server_bit'] = round($result['client_server']/$price,2);
        $result['client_server_msg'] = '减除品牌方的分成'.$client['client'].'元 后乘以'.((1-$bit)*100).'%,等于'.$mytemp.'元,再乘以'.(($client_server_bit)*100).'%等于'.$result['client_server'].'元';
    }
    if(true){
        $result['lawyer_intro'] = $lawyer_intro;
        $result['lawyer_intro_bit'] = round($result['lawyer_intro']/$price,2);
        $result['lawyer_intro_msg'] = '减除品牌方的分成'.($client['client']+$server).'元和律所分成'.$lawyer.'后乘以'.($lawyer_intro_bit*100).'%,等于'.$result['lawyer_intro'].'元';
    }


    $result['total'] = $result['client'] + $result['my'] + $result['lawyer'] + $result['client_server'] + $result['lawyer_intro'];
    $result['total_bit'] = round($result['my_bit'] + $result['lawyer_bit'] + $result['client_bit'] + $result['lawyer_intro_bit'] + $result['client_server_bit'],1);
    if($result['total_bit'] != 1 || $result['total'] != $price){
        $result['error'] = '计算规则公式有问题，请修正';
    }
    $temp_reward = $result['lawyer'] - $reward;
    $lawyer_money = [
        'bit_money'=>$temp_reward * 0.3,
        'chengben_money'=>$temp_reward * 0.45,
        'fuwu_money'=>$temp_reward * 0.25,
        'dsxia_money'=>0,
    ];
    if($min_money > 1000 && $min_money < $price){
        $fenbit_temp = $fenbit;
        $fenbit_temp['price'] = $min_money;
        $min_result = lawyer_money($params_str,$fenbit_temp);
        $lawyer_money = [
            'bit_money'=>bcmul($min_result['lawyer'],0.3,2),//$min_result['lawyer'] * 0.3, //分成比例金额
            'chengben_money'=>bcmul($min_result['lawyer'],0.45,2),//$min_result['lawyer'] * 0.45, //案件成本金额
            'fuwu_money'=>bcmul($min_result['lawyer'],0.25,2),//$min_result['lawyer'] * 0.25, // 律师服务费
            'dsxia_money'=>bcsub($temp_reward,$min_result['lawyer'],2),//$temp_reward - $min_result['lawyer'], //平台额外奖励金额
        ];
    }
    $lawyer_money['total_money'] = $lawyer_money['bit_money'] + $lawyer_money['chengben_money'] + $lawyer_money['fuwu_money'] + $lawyer_money['dsxia_money'];
    $lawyer_money['test_money'] = $min_money ? round($min_money*1.2,0) : 10000;
    $lawyer_money['reward_money'] = $reward; //奖励金额

    $result['lawyer_view'] = $lawyer_money;

    if($min_money > 30000){
        $result['proposal'] = '建议：采用购买公证+律师函+和解的方式进行,和解不成可以考虑【直接立案诉讼】';
    }else if($min_money > 10000){
        $result['proposal'] = '建议：采用时间戳取证+律师函+和解的方式进行,和解不成【可以考虑是否立案诉讼】';
    }else{
        $result['proposal'] = '建议：采用时间戳取证+律师函+和解+诉前调解+庭前调解的方式进行';
    }

    return $result;
}
/**
 * @desc 替换变量 获取发送内容
 * @param $tpl_content 模板内容
 * @param $var_arr 变量列表
 * author eRic
 * dateTime 2022-02-11 14:44
 */
function ReplaceVar($tpl_content,$var_arr){
    foreach ($var_arr as $key => $vals){
        $tpl_content = str_replace('{'.$key.'}',$vals,$tpl_content);
    }
    return $tpl_content;
}

/**
 * @desc 通过模板获取变量数组
 * @param $tpl_content 含变量的模板内容
 * @param $str |分隔的字符串
 * author eRic
 * dateTime 2022-02-17 19:15
 */
function strVarArr($tpl_content,$str){
    $tpl_var = explode('|',$str);
    preg_match_all('/{(.*?)}/', $tpl_content, $results);
    $new_items = [];
    if(!empty($results[1])){
        foreach ($results[1] as $key => $names){
            if(!empty($tpl_var[$key])){
                $new_items[$names] = $tpl_var[$key];
            }
        }
    }
    if(count($results[1]) != count($new_items)){
        return false;
    }
    return $new_items;
}
// 获取文件路径扩展名
function getPathExtensionName($path){
    $pathinfo = pathinfo($path);
    if(empty($pathinfo['extension'])){
        return false;
    }
    return $pathinfo['extension'];
}

// 返回资源类型图片
function returnFileType($path){
    $pathinfo = pathinfo($path);
    if(empty($pathinfo['extension'])){
        return $path;
    }
    $fileExtension = $pathinfo['extension'];
    $img1 = '';
    if($fileExtension == 'jpg' || $fileExtension == 'jpeg' || $fileExtension == 'png' || $fileExtension == 'bmp'){
        if(strpos($path,'400x400') === false){
            return $path.'?x-oss-process=style/400x400.jpg';
        }
        return $path;
    }
    if($fileExtension == 'xlsx' || $fileExtension == 'xls'){
        $img1 = '/assets/assets/images/excel-icon.png';
    }else if($fileExtension == 'zip'){
        $img1 = '/assets/assets/images/zip-icon.png';
    }else if($fileExtension == 'pdf'){
        $img1 = '/assets/assets/images/pdf-icon.png';
    }else if($fileExtension == 'word'){
        $img1 = '/assets/assets/images/word-icon.png';
    }else if($fileExtension == 'rar'){
        $img1 = '/assets/assets/images/rar-icon.png';
    }else if($fileExtension == 'docx'){
        $img1 = '/assets/assets/images/doc-icon.png';
    }else if($fileExtension == 'doc'){
        $img1 = '/assets/assets/images/doc-icon.png';
    }else if($fileExtension == 'mp4'){
        $img1 = '/assets/assets/images/mp4-icon.png';
    }else if($fileExtension == 'jpg' || $fileExtension == 'jpeg' || $fileExtension == 'png'){
        $img1= '/assets/assets/images/notimg.png';
    }else{
        $img1= '/assets/assets/images/l_load.png';
    }
    return $img1;
}
function getContentRep($content) {
    preg_match_all("/src='(.*?)'/",$content,$pm);
    if(!empty($pm[1])){
        $path_arr = array_flip(array_flip($pm[1]));
        foreach ($path_arr as $path){
            if(strpos($content,'data-magnify') === false){
                $content = str_replace('src=\''.$path.'\'','src="'.returnFileType($path).'" data-magnify="gallery" data-group="g1" data-caption="文件预览" data-src="'.returnFileType($path).'"',$content);
            }else{
                $content = str_replace('src=\''.$path,'src=\''.returnFileType($path),$content);
            }
        }
    }
    $content = str_replace('//up','/up',$content);
    if(strpos($content,'dsxiacase') === false){
        if(strpos($content,'case/') !== false){
            if(strpos($content,'public/uploads/newcase/') === false){
                $content = str_replace('case/',env('FILE_SOURCE').'/case/',$content);
            }else{
                if(strpos($content,'/public') === false){
                    $content = str_replace('public',env('FILE_SOURCE').'/public',$content);
                }else{
                    $content = str_replace('/public',env('FILE_SOURCE').'/public',$content);
                }
            }
        }else{
            if(strpos($content,'/public') === false){
                $content = str_replace('public',env('FILE_SOURCE').'/public',$content);
            }else{
                if(strpos($content,'/public/storage/casezip/') !== false){
                    $content = str_replace('/public',env('APP_URL').'/public',$content);
                }else {
                    $content = str_replace('/public', env('FILE_SOURCE') . '/public', $content);
                }
            }
        }
    }




    // https://dsxiacase.oss-cn-hangzhou.aliyuncs.com/
    //$content = str_replace('https://dsxiacase.oss-cn-hangzhou.aliyuncs.com/','',$content);
    if(strpos($content,'data-magnify') === false){
        $content = str_replace('img src','img data-magnify="gallery" data-group="g1" data-caption="文件预览" src',$content);
    }
    return $content;
}


if (!function_exists('switchFileUrl')) {
    // 转换文件url
    function switchFileUrl($weburl){
        $extension =  !empty(pathinfo($weburl)['extension'])?pathinfo($weburl)['extension']:'';
        if(empty($extension)){
            return $weburl;
        }

        $weburl =  str_replace('//up','/up',$weburl);
        if(strpos($weburl,'/pu') === false){
            $weburl = '/'.$weburl;
        }
        if(strpos($weburl,'/public/storage/casezip/') !== false){
            $newweburl = env('APP_URL').$weburl;
        }else{
            $newweburl = env('FILE_SOURCE').$weburl;
        }

        $extension_type = ['docx','dotx','doc','xlsx','xlsb','xls','xlsm','pptx','ppsx','ppt','pps','potx','ppsm'];
        if(in_array($extension,$extension_type)){
            $newweburl = 'https://view.officeapps.live.com/op/view.aspx?src='.$newweburl;
        }
       return $newweburl;
    }
}
if (!function_exists('showFileUrl')) {
    // 展示材料地址
    function showFileUrl($weburl){
        $newweburl = $weburl;
        if(file_exists(base_path($weburl))){
            return $weburl;
        }
        if(strpos($weburl,'http') === false){
            if(strpos($weburl,'/pu') === false){
                $weburl = '/'.$weburl;
            }
            $weburl = str_replace('//up','/up',$weburl);
            $extension =  !empty(pathinfo($weburl)['extension'])?pathinfo($weburl)['extension']:'';
            if(empty($extension)){
                return $weburl;
            }
            $extension_type = ['jpg','jpeg','png'];

            if(in_array($extension,$extension_type)){
                $weburl = $weburl.'?x-oss-process=style/400x400.jpg';
            }
            $newweburl = env('FILE_SOURCE').$weburl;
        }
        return $newweburl;
    }
}
if (!function_exists('formatDates')) {
    function formatDates($time){
        $t=time()-strtotime($time);
        $f=[
            '31536000'=>'年',
            '2592000'=>'个月',
            '604800'=>'星期',
            '86400'=>'天',
            '3600'=>'小时',
            '60'=>'分钟',
            '1'=>'秒'
        ];
        foreach ($f as $k=>$v)    {
            if (0 != $c = floor($t/(int)$k)) {
                return $c.$v.'前';
            }
        }
    }
}

/**
 * @desc 把字符串中间隐藏
 * @param $str
 * @return string
 * author eRic
 * dateTime 2020/9/12 4:16 下午
 */
function replaceStar($str,$start = 2,$end = -1)
{
    if (preg_match("/[\x{4e00}-\x{9fa5}]+/u", $str)) {
        //按照中文字符计算长度
        $len = mb_strlen($str, 'UTF-8');
        //echo '中文';
        if ($len >= 3 && $len <= 6) {
            //三个字符或三个字符以上掐头取尾，中间用*代替
            $str = mb_substr($str, 0, $start, 'UTF-8') . '****' . mb_substr($str, $end, 1, 'UTF-8');
        } elseif ($len >= 5) {
            //两个字符
            $str = mb_substr($str, 0, $start, 'UTF-8') . '****' . mb_substr($str, $end, 2, 'UTF-8');
        }elseif ($len == 2) {
            //两个字符
            $str = mb_substr($str, 0, 1, 'UTF-8') . '****';
        }
    } else {
        //按照英文字串计算长度
        $len = strlen($str);
        //echo 'English';
        if ($len >= 3 && $len <= 6) {
            //三个字符或三个字符以上掐头取尾，中间用*代替
            $str = substr($str, 0, $start) . '****' . substr($str, $end);
        } elseif ($len >= 5) {
            //两个字符
            $str = substr($str, 0, $start) . '****' . substr($str, $end);
        }elseif ($len == 2) {
            //两个字符
            $str = substr($str, 0, 1) . '****';
        }
    }
    return $str;
}

/**
 * @desc 两个日期相差天数
 * @param $time1
 * @param $time2
 * author eRic
 * dateTime 2022-10-18 21:05
 */
function two_time_diff_days($time11,$time22){
    $time1 = strtotime($time11);
    $time2 = strtotime($time22);
    $diff_seconds = $time1 - $time2;
    $diff_days = floor($diff_seconds/86400);
    return $diff_days;
}

function isjson($string) {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}

/** 获取用户最近的店铺
 * @param $shopList
 * @param $lon
 * @param $lat
 * @return array
 */
function nearestShop($shopList, $lon, $lat){
    $arr = [];
    foreach ($shopList as $key => $shop){
        $arr[$key] = getDistance($lon, $lat, $shop->lon, $shop->lat);
    }
    asort($arr);    //按距离排序
    return $shopList[array_keys($arr)[0]];
}

/** 根据坐标计算距离
 * @param float $lon1
 * @param float $lat1
 * @param float $lon2
 * @param float $lat2
 * @param int $unit 单位 2是公里
 * @param int $decimal 四舍五入小数点后位数
 * @return float
 */
function getDistance($lon1, $lat1, $lon2, $lat2, $unit = 2, $decimal = 2){
    $EARTH_RADIUS = 6371; // 地球半径系数

    //将角度转为狐度
    $radLng1 = deg2rad($lon1);
    $radLat2 = deg2rad($lat2);
    $radLat1 = deg2rad($lat1);
    $radLng2 = deg2rad($lon2);

    $distance = 2 * asin(sqrt(pow(sin(($radLat1-$radLat2) / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin(($radLng1-$radLng2) / 2), 2))) * $EARTH_RADIUS * 1000;

    if ($unit === 2) {
        $distance /= 1000;
    }
    return round($distance, $decimal);
}

function wxopen(){
    $wxopen_config = Setting::getlists([], 'wxopen');
    $open_config   = [
        'app_id'  => $wxopen_config['wxopen_app_id'],
        'secret'  => $wxopen_config['wxopen_secret'],
        'token'   => !empty($wxopen_config['wxopen_Token']) ? $wxopen_config['wxopen_Token'] : '',
        'aes_key' => !empty($wxopen_config['wxopen_aesKey']) ? $wxopen_config['wxopen_aesKey'] : ''
    ];
    $openPlatform  = Factory::openPlatform($open_config);
    return $openPlatform;
}

function wechatPay($hotel_id){
    $config = \App\Models\Hotel\WxappConfig::getConfig($hotel_id);
    return $app    = Factory::payment($config);
}

if (!function_exists('alipay_or_weixin')) {
    /**
     * 验证是不是支付宝或微信客户端
     * @return $headerinfo (false or )
     */
    function alipay_or_weixin()
    {
        $headerinfo = false;
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient')) {
            $headerinfo = "alipay";
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            $headerinfo = "weixin";
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'QQ')) {
            $headerinfo = "qqpay";
        }
        return $headerinfo;
    }
}

// 转成小驼峰命名法
function camelCase($string) {
    $words = explode('_', $string);
    $camelCaseString = $words[0];

    for ($i = 1; $i < count($words); $i++) {
        $camelCaseString .= ucfirst($words[$i]);
    }

    return lcfirst($camelCaseString);
}

function convertDateRange($date1,$date2) {
    //$dates = explode(' - ', $dateRange);
    $startDate = date('n月j日', strtotime($date1));
    $endDate = date('n月j日', strtotime($date2));

    return $startDate . '~' . $endDate;
}

function formatFloats($number) {
    // 判断是否有小数部分
    if ($number == floor($number)) {
        return intval($number); // 没有小数部分，转换为整数
    } else {
        return $number; // 有小数部分，保留小数
    }
}
// 获取两个日期之前的所有日期
function getDatesInRange($startDate, $endDate) {
    $dates = array();
    $currentDate = strtotime($startDate);

    while ($currentDate <= strtotime($endDate)) {
        $dates[] = date('Y-m-d', $currentDate);
        $currentDate = strtotime('+1 day', $currentDate);
    }
    return $dates;
}

// 判断是不是周末
function isWeekend() {
    $today = date('w'); // 获取今天是星期几（0-6，0代表星期日）
    if ($today == 0 || $today == 6) {
        return true; // 今天是周末
    } else {
        return false; // 今天不是周末
    }
}
// 获取指定日期范围内的所有周末日期
function getWeekendDatesInRange($startDate, $endDate) {
    $weekendDates = array();
    $currentDate = strtotime($startDate);

    while ($currentDate <= strtotime($endDate)) {
        $dayOfWeek = date('w', $currentDate); // 获取当前日期是星期几（0-6，0代表星期日）

        if ($dayOfWeek == 5 || $dayOfWeek == 6) {
            $weekendDates[] = date('Y-m-d', $currentDate); // 如果是星期六或星期日，加入数组
        }

        $currentDate = strtotime('+1 day', $currentDate); // 增加一天
    }
    return $weekendDates;
}

// 格式化浮点数值
if (!function_exists('formatFloat')) {
    function formatFloat($number) {
        // 检查是否为整数
        if ($number == intval($number)) {
            return intval($number); // 返回整数部分
        } else {
            // 去除末尾的0
            return (float)rtrim(rtrim(number_format($number, 2, '.', ''), '0'), '.');
        }

    }
}



