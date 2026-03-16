<?php

namespace App\Services;
use Illuminate\Notifications\Messages\MailMessage;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

/**
 * 生成海报
 * Class CunZhengService
 * @package App\Services
 * anthor Fox
 */
class HaibaoService {

    public $tempID;

    public function __construct($id)
    {
        $this->tempID = $id;
    }

    public function Handle($name, $shijian, $didian, $rensu, $imgurl)
    {
        $config = [];
        switch ($this->tempID) {
            case '1' :
                $config = $this->temp1($name, $shijian, $didian, $rensu, $imgurl);
                break;
            case '2' :
                $config = $this->temp2($name, $shijian, $didian, $rensu, $imgurl);
                break;
            case '3' :
                $config = $this->temp3($name, $shijian, $didian, $rensu, $imgurl);
                break;
            case '4' :
                $config = $this->temp4($name, $shijian, $didian, $rensu, $imgurl);
                break;
            default :
                $config = $this->temp($name, $shijian, $didian, $rensu, $imgurl);
        }

        return $config;
    }

    public function temp($name, $shijian, $didian, $rensu, $imgurl)
    {
        $fontSize = 18;
        if (mb_strlen($name, 'utf-8') > 14) {
            $fontSize = 16;
        }
        /**
         * 'text'       => array(
        array(
        'text'      => $name,
        'left'      => 30,
        'top'       => 350,
        'fontPath'  => public_path('fonts/haibao02.ttf'),     //字体文件captcha/font/Elephant.ttf
        'fontSize'  => $fontSize,             //字号
        'fontColor' => '0,0,0',       //字体颜色
        'angle'     => 0,
        ),
        array(
        'text'      => '比赛时间:' . $shijian,
        'left'      => 30,
        'top'       => 390,
        'fontPath'  => public_path('fonts/simhei.ttf'),     //字体文件captcha/font/Elephant.ttf
        'fontSize'  => 12,             //字号
        'fontColor' => '0,0,0',       //字体颜色
        'angle'     => 0,
        ),
        array(
        'text'      => '比赛地点:' . $didian,
        'left'      => 30,
        'top'       => 410,
        'fontPath'  => public_path('fonts/simhei.ttf'),     //字体文件captcha/font/Elephant.ttf
        'fontSize'  => 12,             //字号
        'fontColor' => '0,0,0',       //字体颜色
        'angle'     => 0,
        ),
        array(
        'text'      => '限报人数: ' . $rensu . ' 人',
        'left'      => 30,
        'top'       => 430,
        'fontPath'  => public_path('fonts/simhei.ttf'),     //字体文件captcha/font/Elephant.ttf
        'fontSize'  => 12,             //字号
        'fontColor' => '0,0,0',       //字体颜色
        'angle'     => 0,
        ),
        ),
         */
        $config = array(
            'text' => [],
            'image'      => array(
                array(
                    'url'     => $imgurl,     //二维码资源
                    'stream'  => 0,
                    'left'    => 150,
                    'top'     => -55,
                    'right'   => 0,
                    'bottom'  => 0,
                    'width'   => 130,
                    'height'  => 130,
                    'opacity' => 100
                ),
            ),
            'background' => public_path('/img/share/share4.png')          //400X600.png背景图
        );

        return $config;
    }

    public function temp1($name, $shijian, $didian, $rensu, $imgurl)
    {
        $fontSize = 18;
        if (mb_strlen($name, 'utf-8') > 14) {
            $fontSize = 16;
        }
        $config1 = array(
            'text' => [],
            'image'      => array(
                array(
                    'url'     => $imgurl,     //二维码资源
                    'stream'  => 0,
                    'left'    => 102,
                    'top'     => -87,
                    'right'   => 0,
                    'bottom'  => 0,
                    'width'   => 130,
                    'height'  => 130,
                    'opacity' => 100
                ),
            ),
            'background' => public_path('/img/share/share4.png')          //400X600.png背景图
        );

        return $config1;
    }

    /*public function temp2($name, $shijian, $didian, $rensu, $imgurl)
    {
        $fontSize = 18;
        if (mb_strlen($name, 'utf-8') > 14) {
            $fontSize = 16;
        }
        $config2 = array(
            'text'       => array(
                array(
                    'text'      => $name,
                    'left'      => 30,
                    'top'       => 505,
                    'fontPath'  => 'fonts/haibao02.ttf',     //字体文件captcha/font/Elephant.ttf
                    'fontSize'  => $fontSize,             //字号
                    'fontColor' => '0,0,0',       //字体颜色
                    'angle'     => 0,
                ),
                array(
                    'text'      => '比赛时间:' . $shijian,
                    'left'      => 30,
                    'top'       => 540,
                    'fontPath'  => 'fonts/simhei.ttf',     //字体文件captcha/font/Elephant.ttf
                    'fontSize'  => 12,             //字号
                    'fontColor' => '0,0,0',       //字体颜色
                    'angle'     => 0,
                ),
                array(
                    'text'      => '比赛地点:' . $didian,
                    'left'      => 30,
                    'top'       => 560,
                    'fontPath'  => 'fonts/simhei.ttf',     //字体文件captcha/font/Elephant.ttf
                    'fontSize'  => 12,             //字号
                    'fontColor' => '0,0,0',       //字体颜色
                    'angle'     => 0,
                ),
                array(
                    'text'      => '限报人数: ' . $rensu . ' 人',
                    'left'      => 30,
                    'top'       => 580,
                    'fontPath'  => ROOT_PATH . '/fonts/simhei.ttf',     //字体文件captcha/font/Elephant.ttf
                    'fontSize'  => 12,             //字号
                    'fontColor' => '0,0,0',       //字体颜色
                    'angle'     => 0,
                ),
            ),
            'image'      => array(
                array(
                    'url'     => $imgurl,     //二维码资源
                    'stream'  => 0,
                    'left'    => 157,
                    'top'     => -210,
                    'right'   => 0,
                    'bottom'  => 0,
                    'width'   => 110,
                    'height'  => 110,
                    'opacity' => 100
                ),
            ),
            'background' => 'images/haibao/2.jpg'          //400X600.png背景图
        );

        return $config2;
    }

    public function temp3($name, $shijian, $didian, $rensu, $imgurl)
    {
        $fontSize = 18;
        if (mb_strlen($name, 'utf-8') > 14) {
            $fontSize = 16;
        }
        $config3 = array(
            'text'       => array(
                array(
                    'text'      => $name,
                    'left'      => 30,
                    'top'       => -100,
                    'fontPath'  => 'fonts/haibao02.ttf',     //字体文件captcha/font/Elephant.ttf
                    'fontSize'  => $fontSize,             //字号
                    'fontColor' => '0,0,0',       //字体颜色
                    'angle'     => 0,
                ),
                array(
                    'text'      => '比赛时间:' . $shijian,
                    'left'      => 30,
                    'top'       => -70,
                    'fontPath'  => 'fonts/simhei.ttf',     //字体文件captcha/font/Elephant.ttf
                    'fontSize'  => 12,             //字号
                    'fontColor' => '0,0,0',       //字体颜色
                    'angle'     => 0,
                ),
                array(
                    'text'      => '比赛地点:' . $didian,
                    'left'      => 30,
                    'top'       => -50,
                    'fontPath'  => 'fonts/simhei.ttf',     //字体文件captcha/font/Elephant.ttf
                    'fontSize'  => 12,             //字号
                    'fontColor' => '0,0,0',       //字体颜色
                    'angle'     => 0,
                ),
                array(
                    'text'      => '限报人数: ' . $rensu . ' 人',
                    'left'      => 30,
                    'top'       => -30,
                    'fontPath'  => ROOT_PATH . '/fonts/simhei.ttf',     //字体文件captcha/font/Elephant.ttf
                    'fontSize'  => 12,             //字号
                    'fontColor' => '0,0,0',       //字体颜色
                    'angle'     => 0,
                ),
            ),
            'image'      => array(
                array(
                    'url'     => $imgurl,     //二维码资源
                    'stream'  => 0,
                    'left'    => 26,
                    'top'     => 115,
                    'right'   => 0,
                    'bottom'  => 0,
                    'width'   => 120,
                    'height'  => 120,
                    'opacity' => 100
                ),
            ),
            'background' => 'images/haibao/3.jpg'          //400X600.png背景图
        );

        return $config3;
    }

    public function temp4($name, $shijian, $didian, $rensu, $imgurl)
    {
        $fontSize = 18;
        if (mb_strlen($name, 'utf-8') > 14) {
            $fontSize = 16;
        }
        $config4 = array(
            'text'       => array(
                array(
                    'text'      => $name,
                    'left'      => 65,
                    'top'       => 215,
                    'fontPath'  => 'fonts/AaBanSong.ttf',     //字体文件captcha/font/Elephant.ttf
                    'fontSize'  => $fontSize,             //字号
                    'fontColor' => '0,0,0',       //字体颜色
                    'angle'     => 0,
                    'isTitle' => 1
                ),
                array(
                    'text'      => '比赛时间:' . $shijian,
                    'left'      => 65,
                    'top'       => 240,
                    'fontPath'  => 'fonts/simhei.ttf',     //字体文件captcha/font/Elephant.ttf
                    'fontSize'  => 12,             //字号
                    'fontColor' => '0,0,0',       //字体颜色
                    'angle'     => 0,
                ),
                array(
                    'text'      => '比赛地点:' . $didian,
                    'left'      => 65,
                    'top'       => 260,
                    'fontPath'  => 'fonts/simhei.ttf',     //字体文件captcha/font/Elephant.ttf
                    'fontSize'  => 12,             //字号
                    'fontColor' => '0,0,0',       //字体颜色
                    'angle'     => 0,
                ),
                array(
                    'text'      => '限报人数: ' . $rensu . ' 人',
                    'left'      => 65,
                    'top'       => 280,
                    'fontPath'  => ROOT_PATH . '/fonts/simhei.ttf',     //字体文件captcha/font/Elephant.ttf
                    'fontSize'  => 12,             //字号
                    'fontColor' => '0,0,0',       //字体颜色
                    'angle'     => 0,
                ),
            ),
            'image'      => array(
                array(
                    'url'     => $imgurl,     //二维码资源
                    'stream'  => 0,
                    'left'    => 145,
                    'top'     => -60,
                    'right'   => 0,
                    'bottom'  => 0,
                    'width'   => 110,
                    'height'  => 110,
                    'opacity' => 100
                ),
            ),
            'background' => 'images/haibao/4.jpg'          //400X600.png背景图
        );

        return $config4;
    }*/


}
