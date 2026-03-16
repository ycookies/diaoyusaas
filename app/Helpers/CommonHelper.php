<?php
/**
 * 打印对象的相关信息
 */
if (!function_exists('idump')) {
    function idump($var, $echo = true, $label = null, $strict = true)
    {
        $label = ($label === null) ? '' : rtrim($label) . ' ';
        if (!$strict) {
            if (ini_get('html_errors')) {
                $output = print_r($var, true);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            } else {
                $output = $label . ' : ' . print_r($var, true);
            }
        } else {
            ob_start();
            var_dump($var);
            $output = ob_get_clean();
            if (!extension_loaded('xdebug')) {
                $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            }
        }
        if ($echo) {
            echo ($output);
            return null;
        } else {
            return $output;
        }
    }
}

/**
 * 清空数组中的空元素
 */
if (!function_exists('arrayTrim')) {
    function arrayTrim($arr)
    {
        return array_filter($arr, create_function('$v', 'return ! empty($v);'));
    }
}

/**
 * 产生随机字串，可用来自动生成密码 默认长度6位 字母和数字混合
 * @param string $len 长度
 * @param string $type 字串类型
 * @param string $addChars 额外字符
 * @return string
 */
if (!function_exists('randStr')) {
    function randStr($len = 6, $type = 0, $addChars = '')
    {
        $str = '';
        switch ($type) {
            case 1:
                $chars = str_repeat('0123456789', 3);
                break;
            case 2:
                $chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
                break;
            case 3:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
                break;
            case 4:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
                break;
            case 5:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789' . $addChars;
                break;
            default: // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
                $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
                break;
        }
        if ($len > 10) {
            //位数过长重复字符串一定次数
            $chars = ($type == 1) ? str_repeat($chars, $len) : str_repeat($chars, 5);
        }
        $chars = str_shuffle($chars);
        $str = substr($chars, 0, $len);
        return $str;
    }
}

/**
 * 10进制转2、8、16、36、62进制
 * @param int $num 要转换的数字
 * @param int $hex 要转换的进制(2,8,16,36,62)
 * @param int $length 字符宽度
 * @return string
 */
if (!function_exists('hexTrans')) {
    function hexTrans($num, $hex = 62, $length = 0)
    {
        if (!in_array($hex, [2, 8, 16, 36, 62])) {
            return strval($num);
        }
        if ($hex == 2) {
            return decbin($num);
        }
        if ($hex == 8) {
            return decoct($num);
        }
        if ($hex == 16) {
            return dechex($num);
        }
        if ($hex == 36) {
            return base_convert($num, 10, 36);
        }

        $str = '';
        while ($num != 0) {
            $n = $num % $hex;
            switch ($hex) {
                case 62:
                    if (($n >= 10) && ($n <= 35)) {
                        $str .= chr($n + 55);
                        break;
                    }
                    if (($n >= 36) && ($n <= 61)) {
                        $str .= chr($n + 61);
                        break;
                    }
                    $str .= $n;
                    break;
            }
            $num = intval($num / $hex);
        }
        return strrev(str_pad($str, $length, '0', STR_PAD_RIGHT));
    }
}

/**
 * 使用另一个字符串填充字符串前缀为指定长度
 */
if (!function_exists('padLeft')) {
    function padLeft($str, $length)
    {
        return str_pad($str, $length, '0', STR_PAD_LEFT);
    }
}

/**
 * 使用另一个字符串填充字符串后缀为指定长度
 */
if (!function_exists('padRight')) {
    function padRight($str, $length)
    {
        return str_pad($str, $length, '0', STR_PAD_RIGHT);
    }
}

/**
 * 获取文件扩展名
 */
if (!function_exists('getFileExt')) {
    function getFileExt($file = '')
    {
        return strtolower(pathinfo($file, PATHINFO_EXTENSION));
    }
}

/**
 * 图片缩略图文件名
 */
if (!function_exists('getThumb')) {
    function getThumb($file = '')
    {
        if (empty($file)) {
            return;
        }
        extract(pathinfo($file));
        return $dirname . '/' . $filename . '_thumb.' . $extension;
    }
}

/**
 * 字符串截取
 */
if (!function_exists('cutStr')) {
    function cutStr($str, $len)
    {
        $str = strip_tags($str);
        for ($i = 0; $i < $len; $i++) {
            $temp_str = substr($str, 0, 1);
            if (ord($temp_str) > 127) {
                $i++;
                if ($i < $len) {
                    $new_str[] = substr($str, 0, 3);
                    $str = substr($str, 3);
                }
            } else {
                $new_str[] = substr($str, 0, 1);
                $str = substr($str, 1);
            }
        }
        return htmlspecialchars(join($new_str), ENT_QUOTES);
    }
}

/**
 * 字符串截取，并附加省略号
 */
if (!function_exists('cutStr2')) {
    function cutStr2($str, $len)
    {
        $len1 = strlen($str);
        $str = strip_tags($str);
        for ($i = 0; $i < $len; $i++) {
            $temp_str = substr($str, 0, 1);
            if (ord($temp_str) > 127) {
                $i++;
                if ($i < $len) {
                    $new_str[] = substr($str, 0, 3);
                    $str = substr($str, 3);
                }
            } else {
                $new_str[] = substr($str, 0, 1);
                $str = substr($str, 1);
            }
        }

        $new_str = join($new_str);
        if (strlen($new_str) < $len1) {
            $new_str .= '…';
        }
        return htmlspecialchars($new_str, ENT_QUOTES);
    }
}

/**
 * 将特殊字符转换为 HTML 实体
 */
if (!function_exists('tt')) {
    function tt($str)
    {
        return htmlspecialchars($str, ENT_QUOTES);
    }
}

/**
 * 获取指定长度的字符串
 */
if (!function_exists('safe')) {
    function safe($str, $length = 100)
    {
        return mb_substr(trim($str), 0, $length, 'utf-8');
    }
}

/**
 * 过滤特殊HTML标签
 */
if (!function_exists('safe2')) {
    function safe2($str)
    {
        return htmlFilter($str);
    }
}

/**
 * 过滤特殊HTML标签
 */
if (!function_exists('htmlFilter')) {
    function htmlFilter($str)
    {
        $search = array("'<script[^>]*?>(.*?)</script>'i",
            "'(javascript|jscript|vbscript|vbs):'i",
            "'<iframe(.*)>.*</iframe>'i",
            "'<frameset(.*)>.*</frameset>'i",
            "'on(load|exit|error|mouse|key|click)'i");
        $replace = array("\\1", "", "", "", "");
        return preg_replace($search, $replace, $str);
    }
}

/**
 * 自动排版
 */
if (!function_exists('formatMatch')) {
    function formatMatch($str)
    {
        $str = strip_tags($str, '<p><br>');
        $search = [
            '/<p[^>]*?>(.*?)<\/p>/i',
            '/<br[^>]*?>/i',
            '/[&nbsp;]*/i',
        ];
        $replace = ["\\1\n\n", "\n\n", ''];
        return preg_replace($search, $replace, $str);
    }
}

/**
 * 在字符串所有新行之前插入 HTML 段落标记
 * @param string $string String to be formatted.
 * @param boolean $line_breaks When true, single-line line-breaks will be converted to HTML break tags.
 * @param boolean $xml When true, an XML self-closing tag will be applied to break tags (<br />).
 * @return string
 */
function nl2p($string, $line_breaks = true, $xml = true)
{
    // Remove existing HTML formatting to avoid double-wrapping things
    $string = str_replace(['<p>', '</p>', '<br>', '<br />'], '', trim($string));
    // It is conceivable that people might still want single line-breaks
    // without breaking into a new paragraph.
    if ($line_breaks == true) {
        return '<p>' . preg_replace(array("/([\n]{2,})/i", "/([^>])\n([^<])/i"), array("</p>\n<p>", '<br' . ($xml == true ? ' /' : '') . '>'), $string) . '</p>';
    } else {
        return '<p>' . preg_replace("/([\n|\r\n]{1,})/i", "</p>\n<p>", $string) . '</p>';
    }
}

/**
 * 限定数值的取值范围
 */
if (!function_exists('toLimitLng')) {
    function toLimitLng($num, $min = 0, $max = PHP_INT_MAX)
    {
        $num = intval($num);
        $min = intval($min);
        $max = intval($max);
        if ($num < $min) {
            return $min;
        }
        if ($num > $max) {
            return $max;
        }
        return $num;
    }
}

/**
 * 日期时间格式化
 */
if (!function_exists('toDate')) {
    function toDate($time, $format = 'Y-m-d H:i:s')
    {
        if (empty($time)) {
            return '';
        }
        return date($format, $time);
    }
}

/**
 * 以百分数形式显示
 */
if (!function_exists('percent')) {
    function percent($p, $t)
    {
        return sprintf('%.2f%%', $p / $t * 100);
    }
}

/**
 * 使用 socket 提交数据
 */
if (!function_exists('postData')) {
    function postData($host, $port, $page, $data)
    {
        $sock = fsockopen($host, $port, $errno, $errstr, 30);
        if (!$sock) {
            return '';
        }

        fwrite($sock, 'POST ' . ($page ? $page . ' ' : '') . "HTTP/1.0\r\n");
        fwrite($sock, 'Host: ' . $host . "\r\n");
        fwrite($sock, "Content-type: application/x-www-form-urlencoded\r\n");
        fwrite($sock, "Content-length: " . strlen($data) . "\r\n");
        fwrite($sock, "Accept: */*\r\n");
        fwrite($sock, "\r\n");
        fwrite($sock, $data);
        $headers = '';
        while ($str = trim(fgets($sock, 4096))) {
            $headers .= $str . "\n";
        }

        $body = '';
        while (!feof($sock)) {
            $body .= fgets($sock, 4096);
        }

        fclose($sock);
        return $body;
    }
}

/**
 * 使用 curl 获取数据
 */
if (!function_exists('curlGetData')) {
    function curlGet($url = '', $header = [])
    {
        if (empty($url)) {
            return false;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            curl_close($ch);
            return curl_error($ch);
        }
        curl_close($ch);
        return $result;
    }
}

/**
 * 使用 curl 提交数据
 */
if (!function_exists('curlPost')) {
    function curlPost($url, $data=[], $header = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if($header){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        if ($result === false) {
            return false;
//            curl_close($ch);
//            echo curl_error($ch);
//            echo curl_getinfo($ch);
        } else {
            curl_close($ch);
            return $result;
        }
    }
}

/**
 * 文件大小格式化
 */
if (!function_exists('filesizeFormat')) {
    function filesizeFormat($size)
    {
        $units = [' B', ' KB', ' MB', ' GB', ' TB'];
        for ($i = 0; $size >= 1024 && $i < 4; $i++) {
            $size /= 1024;
        }
        return round($size, 2) . $units[$i];
    }
}

/**
 * 时间长度格式化
 */
if (!function_exists('timelongFormat')) {
    function timelongFormat($seconds)
    {
        return gmstrftime('%H:%M:%S', $seconds);
    }
}

/**
 * 判断是否手机号
 */
if (!function_exists('isMobile')) {
    function isMobile($str)
    {
        if (strlen($str) != 11 || !preg_match('/^1[3|4|5|6|7|8|9][0-9]\d{4,8}$/', $str)) {
            return false;
        }
        return true;
    }
}

/**
 * 判断是否邮箱
 */
if (!function_exists('isEmail')) {
    function isEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
    }
}

/*
 * 验证IP址址
 */
if (!function_exists('isIp')) {
    function isIp($ip)
    {
        $str = trim($ip);
        if (empty($str)) {
            return false;
        }

        $match = '/^(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])$/';
        return preg_match($match, $str);
    }
}

/**
 * 判断是否身份证号
 */
if (!function_exists('isCardid')) {
    function isCardid($vStr)
    {
        $vCity = [
            '11', '12', '13', '14', '15', '21', '22', '23',
            '31', '32', '33', '34', '35', '36', '37',
            '41', '42', '43', '44', '45', '46', '50', '51', '52', '53', '54',
            '61', '62', '63', '64', '65', '71', '81', '82', '91',
        ];
        if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr)) {
            return false;
        }
        if (!in_array(substr($vStr, 0, 2), $vCity)) {
            return false;
        }
        $vStr = preg_replace('/[xX]$/i', 'a', $vStr);
        $vLength = strlen($vStr);
        if ($vLength == 18) {
            $vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
        } else {
            $vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
        }
        if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday) {
            return false;
        }
        if ($vLength == 18) {
            $vSum = 0;
            for ($i = 17; $i >= 0; $i--) {
                $vSubStr = substr($vStr, 17 - $i, 1);
                $vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr, 11));
            }
            if ($vSum % 11 != 1) {
                return false;
            }
        }
        return true;
    }
}

/**
 * 汉字字符数统计
 */
if (!function_exists('chineseWordCount')) {
    function chineseWordCount($str = '')
    {
        $reg = '/[\x{4E00}-\x{9FA5}]*/u';
        preg_match_all($reg, $str, $arr);
        return mb_strlen(implode(',', array_map('implode', $arr)), 'utf-8');
    }
}

/**
 * 检测文件编码
 * @param string $file 文件路径
 * @return string|null 返回 编码名 或 null
 */
if (!function_exists('getFileCoding')) {
    function getFileCoding($file)
    {
        $arr = ['UTF-8', 'GBK', 'UTF-16LE', 'UTF-16BE', 'ISO-8859-1'];
        $str = file_get_contents($file);
        foreach ($arr as $item) {
            $tmp = mb_convert_encoding($str, $item, $item);
            if (md5($tmp) == md5($str)) {
                return $item;
            }
        }
        return null;
    }
}

/**
 * 自动解析编码读到字符串
 * @param string $str 字符串
 * @param string $charset 读取编码
 * @return string 返回读取内容
 */
if (!function_exists('autoReadText')) {
    function autoReadText($str, $charset = 'UTF-8')
    {
        $arr = ['UTF-8', 'GBK', 'UTF-16LE', 'UTF-16BE', 'ISO-8859-1'];
        foreach ($arr as $item) {
            $tmp = mb_convert_encoding($str, $item, $item);
            if (md5($tmp) == md5($str)) {
                return mb_convert_encoding($str, $charset, $item);
            }
        }
        return $str;
    }
}

/**
 * 根据时间，返回刚刚过去多久
 */
if (!function_exists('justHowLong')) {
    function justHowLong($time)
    {
        $t = time() - $time;
        $f = [
            '31536000' => '年',
            '2592000' => '个月',
            '604800' => '星期',
            '86400' => '天',
            '3600' => '小时',
            '60' => '分钟',
            '1' => '秒',
        ];
        foreach ($f as $k => $v) {
            if (0 != $c = floor($t / intval($k))) {
                return $c . $v . '前';
            }
        }
    }
}
if (!function_exists('get_ip')) {
//不同环境下获取真实的IP
    function getip() {
        $unknown = 'unknown';
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown)){
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        /**
         * 处理多层代理的情况
         * 或者使用正则方式：$ip = preg_match("/[\d\.]{7,15}/", $ip, $matches) ? $matches[0] : $unknown;
         */
        if (false !== strpos($ip, ',')) $ip = reset(explode(',', $ip));
        return $ip;
    }
}

if (!function_exists('convert_arr_key')) {
    function convert_arr_key($arr, $key_name)
    {
        $arr2 = array ();
        foreach ($arr as $key => $val)
        {
            $arr2[$val[$key_name]] = $val;
        }
        return $arr2;
    }
}


if (!function_exists('isMobileBrowser')) {
    function isMobileBrowser()
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA'])) {
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile');
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }
}


if (!function_exists('encrypt')) {
    /*********************************************************************
    函数名称:encrypt
    函数作用:加密解密字符串
    使用方法:
    加密     :encrypt('str','E','nowamagic');
    解密     :encrypt('被加密过的字符串','D','nowamagic');
    参数说明:
    $string   :需要加密解密的字符串
    $operation:判断是加密还是解密:E:加密   D:解密
    $key      :加密的钥匙(密匙);
     *********************************************************************/
    function encrypt($string, $operation, $key = '9FxsQrzbR9MpKFeC')
    {
        $key = md5($key);
        $key_length = strlen($key);
        $string = $operation == 'D' ? base64_decode($string) : substr(md5($string . $key), 0, 8) . $string;
        $string_length = strlen($string);
        $rndkey = $box = array();
        $result = '';
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($key[$i % $key_length]);
            $box[$i] = $i;
        }
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if ($operation == 'D') {
            if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)) {
                return substr($result, 8);
            } else {
                return '';
            }
        } else {
            return str_replace('=', '', base64_encode($result));
        }
    }
}

if(!function_exists('formatHumanDate')) {
    function formatHumanDate($targetTime){
        $todayStart = strtotime(date('Y-m-d') . " 0:0:0");

        $sdefaultDate = date("Y-m-d");
        $first = 1;
        //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
        $w = date('w', strtotime($sdefaultDate));
        //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
        $week_start = date('Ymd',strtotime("$sdefaultDate -".($w ? $w - $first : 6).' days'));

        $arr_week = [
            0 => '星期天',
            1 => '星期一',
            2 => '星期二',
            3 => '星期三',
            4 => '星期四',
            5 => '星期五',
            6 => '星期六'
        ];

        if ($targetTime > $todayStart) {
            $result = date('H:i', $targetTime);
        } elseif (date('Ymd', strtotime('-1 day')) == date('Ymd', $targetTime)) {
            $result = '昨天 ' . date('H:i', $targetTime);
        } elseif (date('Ymd', strtotime('-2 day')) == date('Ymd', $targetTime)) {
            $result = '前天 ' . date('H:i', $targetTime);
        } elseif (date('Ymd', $targetTime) >= $week_start) {
            $result = $arr_week[$w] . date('H:i', $targetTime);
        } elseif (date('Y') == date('Y', $targetTime)) {
            $result = date('m月d日 H:i', $targetTime);
        } elseif (date('Y') > date('Y', $targetTime)) {
            $result = date('Y.m.d H:i', $targetTime);
        }
        return $result;
    }
}

if(!function_exists('passDate')) {
    function passDate($begin_time, $end_time){
        if(empty($end_time)){
            $end_time = time();
        }

        if($begin_time < $end_time){
            $starttime = $begin_time;
            $endtime = $end_time;
        }else{
            $starttime = $end_time;
            $endtime = $begin_time;
        }

        $res = [];
        //计算天数
        $timediff = $endtime - $starttime;
        $days = intval($timediff/86400);
        if($days){
            $res[] = "{$days}天";
        }

        //计算小时数
        $remain = $timediff%86400;
        $hours = intval($remain/3600);
        if($hours){
            $res[] = "{$hours}小时";
        }

        //计算分钟数
        $remain = $remain%3600;
        $mins = intval($remain/60);
        if($mins){
            $res[] = "{$mins}分";
        }

        return [
            'title' => ($begin_time > $end_time ? '-' : '+') . implode("", $res),
            'color' => $begin_time > $end_time ? '#43cf7c' : '#d43030'
        ];
    }
}

/**
 *数字金额转换成中文大写金额的函数
 **/
function toChineseNumber($num){
    $c1 = "零壹贰叁肆伍陆柒捌玖";
    $c2 = "分角元拾佰仟万拾佰仟亿";
    $num = round($num, 2);
    $num = $num * 100;
    if (strlen($num) > 10) {
        return "数据太长，没有这么大的钱吧，检查下";
    }
    $i = 0;
    $c = "";
    while (1) {
        if ($i == 0) {
            $n = substr($num, strlen($num)-1, 1);
        } else {
            $n = $num % 10;
        }
        $p1 = substr($c1, 3 * $n, 3);
        $p2 = substr($c2, 3 * $i, 3);
        if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
            $c = $p1 . $p2 . $c;
        } else {
            $c = $p1 . $c;
        }
        $i = $i + 1;
        $num = $num / 10;
        $num = (int)$num;
        if ($num == 0) {
            break;
        }
    }
    $j = 0;
    $slen = strlen($c);
    while ($j < $slen) {
        $m = substr($c, $j, 6);
        if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
            $left = substr($c, 0, $j);
            $right = substr($c, $j + 3);
            $c = $left . $right;
            $j = $j-3;
            $slen = $slen-3;
        }
        $j = $j + 3;
    }

    if (substr($c, strlen($c)-3, 3) == '零') {
        $c = substr($c, 0, strlen($c)-3);
    }
    if (empty($c)) {
        return "零元整";
    }else{
        return $c . "整";
    }
}

/**
 * 格式化oss文件
 */
if(!function_exists('ossUrl')) {
    function ossUrl($path)
    {
        if(stripos($path, 'http')===false && strlen($path)){
            $url = str_replace("upload/", "", $path);
            $url = "https://jywy.oss-cn-beijing.aliyuncs.com/" . trim($url, '/');
        }
        return $url;
    }
}


if(!function_exists('xmlToArray')) {
    function xmlToArray($xml)
    {
        if(is_file($xml)){ //传的是文件，还是xml的string的判断
            $xml_array=simplexml_load_file($xml);

        }else{

            $xml_array=simplexml_load_string($xml);
        }

        $json = json_encode($xml_array);
        return json_decode($json, true);
    }
}

if(!function_exists('getSeal')) {
    function getSeal($community_id)
    {
        $communit_config = config('chinaums.community');
        if(isset($communit_config[$community_id])){
            $pay_type = $communit_config[$community_id];
        }else{
            $pay_type = 'serven';
        }

        return env('APP_URL') . "/image/seal/{$pay_type}.png";
    }
}

/**
 * 身份证验证规则
 */

if(!function_exists('validateIDCard')) {
    function validateIDCard($idcard){
        if(empty($idcard)){
            return false;
        }else{
            $idcard = strtoupper($idcard); # 如果是小写x,转化为大写X
            if(strlen($idcard) != 18 && strlen($idcard) != 15){
                return false;
            }
            # 如果是15位身份证，则转化为18位
            if(strlen($idcard) == 15){
                # 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
                if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false) {
                    $idcard = substr($idcard, 0, 6) . '18' . substr($idcard, 6, 9);
                } else {
                    $idcard = substr($idcard, 0, 6) . '19' . substr($idcard, 6, 9);
                }
                # 加权因子
                $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
                # 校验码对应值
                $code = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
                $checksum = 0;
                for ($i = 0; $i < strlen($idcard); $i++) {
                    $checksum += substr($idcard, $i, 1) * $factor[$i];
                }
                $idcard = $idcard . $code[$checksum % 11];
            }
            # 验证身份证开始
            $IDCardBody = substr($idcard, 0, 17); # 身份证主体
            $IDCardCode = strtoupper(substr($idcard, 17, 1)); # 身份证最后一位的验证码

            # 加权因子
            $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
            # 校验码对应值
            $code = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
            $checksum = 0;
            for ($i = 0; $i < strlen($IDCardBody); $i++) {
                $checksum += substr($IDCardBody, $i, 1) * $factor[$i];
            }
            $validateIdcard = $code[$checksum % 11];    # 判断身份证是否合理
            if($validateIdcard != $IDCardCode){
                return false;
            }else{
                return true;
            }
        }
    }
}

/**
 * 提取身份证性别
 */

if(!function_exists('getSexByIDCard')) {
    function getSexByIDCard($idcard, $type=1){
        if (empty($idcard)) {
            return 0;
        }
        $sex_int = (int)substr($idcard, 16, 1);
        if ($type == 1) {
            return $sex_int % 2 === 0 ? 2 : 1;
        } else {
            return $sex_int % 2 === 0 ? '女' : '男';
        }
    }
}

/**
 * 提取身份证生日
 */
if(!function_exists('getBirthdayByIDCard')) {
    function getBirthdayByIDCard($idcard){
        if (empty($idcard)) {
            return "";
        }
        return date('Y-m-d', strtotime(substr($idcard, 6, 8)));
    }
}


if(!function_exists('getLocalHost')) {
    function getLocalHost($path){
        if(stripos($path, 'http')===false && strlen($path)){
            $path = env('APP_URL') . "/" . trim($path, '/');
        }
        return $path;
    }
}

if(!function_exists('getDistance')) {
    function getDistance($lng1, $lat1, $lng2, $lat2){
        //将角度转为狐度
        $radLat1 = deg2rad($lat1);//deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137;
        return round(floatval($s), 2);
    }
}

# 汉字转数组
if(!function_exists('cnToArray')) {
    function cnToArray($topictitle){
        $length = mb_strlen($topictitle, 'utf-8');
        $titlearray = [];
        for ($i=0; $i<$length; $i++)
        {
            $titlearray[] = mb_substr($topictitle, $i, 1, 'utf-8');
        }

        return $titlearray;
    }
}

if(!function_exists('base64EncodeImage')) {
    function base64EncodeImage ($image_file) {
        $base64_image = '';
        $image_info = getimagesize($image_file);
        $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
        $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
        return $base64_image;
    }
}
