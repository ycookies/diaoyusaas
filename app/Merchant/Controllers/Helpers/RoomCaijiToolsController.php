<?php

namespace App\Merchant\Controllers\Helpers;

use App\Models\Hotel\OtaDownloadRecords;
use App\Models\Hotel\Room;
use App\Models\Hotel\RoomSkuGift;
use App\Models\Hotel\RoomSkuPrice;
use App\Models\Hotel\RoomSkuTag;
use Dcat\Admin\Admin;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RoomCaijiToolsController extends Controller {

    public function index(Request $request) {
        $url = $this->upImgToOss('/www/wwwroot/hotel.rongbaokeji.com/public/uploads/hotel/143/video/2025-2-28.mp4');
        echo "<pre>";
        print_r($url);
        echo "</pre>";
        exit;
        $roomId        = $request->get('room_id', '10001');
        $json_str      = file_get_contents(public_path('roomjsondata/227/' . $roomId . '.json'));
        $json_arr      = json_decode($json_str, true);
        $room_sku_list = [];
        foreach ($json_arr['rpList'] as $key => $items) {

            $giftPackages = [];
            if (!empty($items['giftPackages'])) {
                $giftPackages = array_map(function ($item) {
                    return [
                        'gift_package_name'        => $item['pkgProductName'], // 礼包名称
                        'gift_package_description' => $item['ruleDescriptionAdditional'], // 礼包描述
                        'pkgSalePrice'             => $item['pkgSalePrice'], // 礼包销售价
                    ];
                }, $items['giftPackages']);
            }

            $room_sku_list[] = [
                'room_sku_title'   => $items['productName'],
                'imgList'          => $items['imgList'], // 相关客房图片
                'personNumDesc'    => $items['personNumDesc'],// 最住几人
                'breakNumDesc'     => $items['breakNumDesc'],// 早餐信息
                'room_sku_price'   => $items['originalPriceRmb'], // 原始销售价
                'additionInfoList' => $items['additionInfoList'],// 客房关键信息 房间面积，床型，有窗无窗/部分有窗，楼层，可入住人数，早餐
                'extraBedInfo'     => $items['extraBedInfo'], // 是否可加床
                'tipsList'         => array_map(function ($item) {
                    return [
                        'tipName'    => $item['tipName'], // 服务标签
                        'tipContent' => $item['tipContent'], // 服务标签
                    ];
                }, $items['tipsList']), // 客房 设施 信息

                'totalPrice' => $items['productPriceInfo']['totalPrice'], //当日销售价


                'giftPackages'          => $giftPackages, //礼包信息 $this->getGiftPackages($items['giftPackages']),
                'productLabelList'      => array_map(function ($item) {
                    return [
                        'productLabelName' => $item['productLabelName'], // 服务标签
                    ];
                }, $items['productLabelList']),//Arr::only($items['productLabelList'], ['productLabelName']), //标签
                'roomBedShowInfo'       => $items['roomBedShowInfo'], // 加床信息
                'roomPropertyShowInfos' => $this->getRoomPropertyShowInfos($items['roomPropertyShowInfos']),
                'roomSpecialInfo'       => $items['roomSpecialInfo'], //接待规则
            ];
        }
        $new_room_data = [
            'room_id'                     => $json_arr['roomId'],
            'room_name'                   => $json_arr['roomInfoName'],
            'minAveragePrice'             => $json_arr['minAveragePrice'], // 最低平均销价
            'area'                        => $json_arr['area'], //  面积
            'bed'                         => $json_arr['bed'], //  床
            'personNum'                   => $json_arr['personNum'],// 能住几人
            'newImgList'                  => $json_arr['newImgList'], //房型图片，第一张是主图
            'coverImageUrl'               => $json_arr['coverImageUrl'], //封面主图
            'roomExtraBedInfo'            => $json_arr['roomExtraBedInfo'], // 是否可加床
            'newTipsList'                 => array_map(function ($item) {
                return [
                    'tipName'    => $item['tipName'], // Tips 名称
                    'tipContent' => $item['tipContent'], // Tips 内容
                ];
            }, $json_arr['newTipsList']), //房型设施,
            'childrenAndExtraBedShowInfo' => $json_arr['childrenAndExtraBedShowInfo'], // 加床相关信息
            'productLabelList'            => array_map(function ($item) {
                return [
                    'productLabelName'     => $item['productLabelName'], // Tips 名称
                    'productLabelSubtitle' => $item['productLabelSubtitle'], // Tips 内容
                ];
            }, $json_arr['productLabelList']),// 房型 label 立即确认,可开专票 等
            'room_sku_list'               => $room_sku_list,
        ];

        //return response()->json($new_room_data);

        $jsonData = $this->handles($new_room_data);

        $res = $this->saveRooms($jsonData);
        return $res;
    }

    public function getRoomPropertyShowInfos($roomPropertyShowInfos) {
        if (empty($roomPropertyShowInfos)) return [];
        $new_property = [];
        foreach ($roomPropertyShowInfos as $key => $itemk) {
            $new_property[] = [
                'facilityTypeName'       => $itemk['facilityTypeName'],// 设施分组名
                'iconUrl'                => $itemk['iconUrl'], // 设施分组图标
                'mRoomTypeFacilityInfos' => array_map(function ($item) {
                    $facilityValue = !empty($item['roomTypeTags'][0]['desc']) ? $item['roomTypeTags'][0]['desc'] : '';
                    if (empty($facilityValue)) {
                        $facilityValue = !empty($item['memo']) ? $item['memo'] : '';
                    }
                    return [
                        'facilityName'  => $item['facilityName'], // 设施名
                        'facilityValue' => $facilityValue
                    ];
                }, $itemk['mRoomTypeFacilityInfos']),// 礼包描述


            ];
        }
        return $new_property;
    }

    // 上传图片到oss
    public function upImgToOss($img_local_path) {
        try {
            $disk = Storage::disk('oss');

            // 检查文件是否存在
            if (!file_exists($img_local_path)) {
                return false;
            }

            // 获取文件信息
            $file_info = pathinfo($img_local_path);
            $extension = $file_info['extension'] ?? '';

            // 生成唯一文件名
            $dir     = 'hotel-room-media';
            $uuids   = date('md') . strtolower(Str::random(6));
            $newName = $uuids . '.' . $extension;
            $path    = "{$dir}/{$newName}";

            // 读取文件内容
            $file_content = file_get_contents($img_local_path);

            // 上传到OSS
            $result = $disk->put($path, $file_content);

            if ($result) {
                // 返回OSS文件访问地址
                return $disk->url($path);
            }

            return false;
        } catch (\Exception $e) {
            \Log::error('Upload to OSS failed: ' . $e->getMessage());
            return false;
        }
    }


    public function handles($json_arr) {
        $jsonData     = json_encode($json_arr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $jsonData_arr = json_decode($jsonData, true);
        $imageUrls    = $this->extractImageUrlsFromJson($jsonData);

        $roomId     = !empty($jsonData_arr['room_id']) ? $jsonData_arr['room_id'] : '0';
        $newdownarr = [];
        $yes_down   = [];
        //建立目录
        if (!is_dir(public_path('/hotelOta/img/' . $roomId))) {
            mkdir(public_path('/hotelOta/img/' . $roomId), 0777, true);
        }
        // 替换原始 JSON 中的图片链接
        foreach ($imageUrls as $imageUrl) {
            // 获取文件名
            $fileName = basename($imageUrl);
            if (strpos($imageUrl, 'Icons') !== false) {
                $localPath = '/hotelOta/icons/' . md5($imageUrl) . '-' . $fileName; // 本地保存路径
            } else {
                $localPath = '/hotelOta/img/' . $roomId . '/' . md5($imageUrl) . '-' . $fileName; // 本地保存路径
            }

            // 已下载的
            if (file_exists(public_path($localPath))) {
                // 查看是否已经上传到了 oss
                $down_r     = OtaDownloadRecords::where(['original_url' => $imageUrl])->first();
                $yes_down[] = [
                    'res_url'         => $imageUrl,
                    'oss_url'         => !empty($down_r->oss_url) ? $down_r->oss_url : '',
                    'local_save_path' => public_path($localPath),
                ];
                continue;
            }
            // 未下载的
            $newdownarr[] = [
                'res_url'         => $imageUrl,
                'oss_url'         => '',
                'local_save_path' => public_path($localPath),
            ];
        }
        // 多线程下载图片
        $no_down_img = [];
        if (!empty($newdownarr)) {

            $this->downloadImages($newdownarr);
            foreach ($newdownarr as &$items) {
                $down_status = 1;
                if (!file_exists($items['local_save_path'])) {
                    $no_down_img[] = $items;
                    $down_status   = 0;
                }
                $web_url = str_replace(public_path(), env('APP_URL'), $items['local_save_path']);
                if (empty($items['oss_url'])) {
                    $oss_url          = $this->upImgToOss($items['local_save_path']);
                    $items['oss_url'] = !empty($oss_url) ? $oss_url : '';
                }

                $insdata = [
                    'hotel_id'     => Admin::user()->hotel_id,
                    'ota_room_no'  => $roomId,
                    'original_url' => $items['res_url'],
                    'local_path'   => $items['local_save_path'],
                    'web_url'      => $web_url,
                    'oss_url'      => $oss_url,
                    'down_status'  => $down_status,
                ];
                OtaDownloadRecords::create($insdata);
            }
        }
        if (!empty($no_down_img)) {
            echo "<pre>";
            echo '以下图片资源没有下载成功';
            print_r($no_down_img);
            echo "</pre>";
            exit;
        }
        $marge_arr = array_merge($yes_down, $newdownarr);
        // 替换内容
        foreach ($marge_arr as $items) {
            if(!empty($items['oss_url'])){
                $web_url = $items['oss_url'];
            }else{
                $web_url = str_replace(public_path(), env('APP_URL'), $items['local_save_path']);
            }

            // 替换
            $jsonData = str_replace($items['res_url'], $web_url, $jsonData); // 替换为本地路径
        }
        return $jsonData;
        //return response()->json(['data'=>$jsonData]);

    }

    // 获取床的数量
    public function getBedCount($value) {
        // 定义床的数量映射
        $bedMapping = [
            '大床' => 1,
            '双床' => 2,
            '三床' => 3
        ];

        // 遍历映射，检查字符串中是否包含关键字
        foreach ($bedMapping as $key => $count) {
            if (strpos($value, $key) !== false) {
                return $count;
            }
        }

        // 如果没有匹配，返回0或其他默认值
        return 1;
    }

    // 获取早餐数量
    public function getBreakfastCount($value) {
        // 定义床的数量映射
        $bedMapping = [
            '无早' => 0,
            '单早' => 1,
            '双早' => 2,
            '三早' => 3
        ];

        // 遍历映射���检查字符串中是否包含关键字
        foreach ($bedMapping as $key => $count) {
            if (strpos($value, $key) !== false) {
                return $count;
            }
        }

        // 如果没有匹配，返回0或其他默认值
        return 0;
    }

    public function getPropertyInfo($property_value) {
        $property_group = [
            'wifi_network'  => '网络与通讯',
            'kefang_buju'   => '客房布局',
            'xiyu_yongpin'  => '卫浴设施',
            'kefang_sheshi' => '客房设施',
            'shipin_yinpin' => '食品饮品',
            'meiti_keji'    => '媒体科技',
            'qingjie_fuwu'  => '清洁服务',
            'bianli_sheshi' => '便利设施',
        ];
        $property_list  = [];
        foreach ($property_value as $key => $item) {
            if ($key == 0) {
                continue;
            }
            if (in_array($item['facilityTypeName'], $property_group)) {
                $property_key  = array_search($item['facilityTypeName'], $property_group);
                $facilityNames = [];
                foreach ($item['mRoomTypeFacilityInfos'] as $infos) {
                    if (isset($infos['facilityName'])) {
                        $facilityNames[] = $infos['facilityName'];
                    }
                }
                $property_list[$property_key] = $facilityNames;//json_encode(,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }
        return $property_list;
    }

    public function getRoomSkuInsertData($skulistinfo) {
        $room_sku = [];
        foreach ($skulistinfo as $keys => $items) {
            $room_sku[] = [
                'roomsku_title'  => $items['room_sku_title'], // 销售标题
                'roomsku_price'  => $items['room_sku_price'], // 日常销售价
                'roomsku_stock'  => 10, // 房量
                'roomsku_zaocan' => $this->getBreakfastCount($items['breakNumDesc']), // 早餐
                'roomsku_tags'   => $items['productLabelList'], //  权益标签
                'roomsku_gift'   => $items['giftPackages'],
            ];
        }
        return $room_sku;
    }

    public function getWindows($windows) {
        if ($windows == '有窗') {
            return '有';
        }
        if (strpos($windows, '没有') !== false) {
            return '没有';
        }
        if (strpos($windows, '部分有') !== false) {
            return '部分有';
        }
        return '没有';
    }

    // 存储到房型表
    public function saveRooms($jsonData) {
        $room_info = json_decode($jsonData, true);

        $additionInfoList     = $room_info['room_sku_list'][0]['additionInfoList'];
        $new_additionInfoList = [];
        foreach ($additionInfoList as $key => $items) {
            $new_additionInfoList[$items['key']] = $items;
        }
        $floor     = !empty($new_additionInfoList['floor']['content']) ? $new_additionInfoList['floor']['content'] : '-';
        $bed_num   = !empty($new_additionInfoList['bed']['content']) ? $new_additionInfoList['bed']['content'] : '-';
        $bed_size  = !empty($new_additionInfoList['bed']['content']) ? $new_additionInfoList['bed']['content'] : '-';
        $people    = !empty($new_additionInfoList['personnum']['content']) ? $new_additionInfoList['personnum']['content'] : '-';
        $windows   = !empty($new_additionInfoList['window']['content']) ? $new_additionInfoList['window']['content'] : '没有';
        $network   = !empty($new_additionInfoList['network']['content']) ? $new_additionInfoList['network']['content'] : 'WIFI免费';
        $xiyan     = !empty($new_additionInfoList['smoke']['content']) ? $new_additionInfoList['smoke']['content'] : '-';
        $breakfast = !empty($new_additionInfoList['breakfast']['content']) ? $new_additionInfoList['breakfast']['content'] : '-';

        $property_info = $this->getPropertyInfo($room_info['room_sku_list'][0]['roomPropertyShowInfos']);
        $room_sku_list = $this->getRoomSkuInsertData($room_info['room_sku_list']);

        preg_match_all('/\d+/', $people, $people_matches);
        $insdata = [
            'hotel_id'      => Admin::user()->hotel_id, // 酒店ID
            'name'          => $room_info['room_name'], // 房型名称
            'name_as'       => 'A' . $room_info['room_id'], // 房间编号
            'price'         => $room_info['minAveragePrice'], //线上价格
            'state'         => 1, // 正常或关闭
            'recommend'     => 0, // 热门推荐
            'logo'          => $room_info['coverImageUrl'], // 主图
            'moreimg'       => $room_info['newImgList'],// 轮播图
            'member_rights' => '-',// 会员权益
            'notes'         => '-', // 政策与服务
            'floor'         => $floor,  //所在楼层
            'area'          => $room_info['area'],// 房型面积
            'people'        => !empty($people_matches[0][0]) ? $people_matches[0][0] : 1,  //能入驻几人
            'bed_num'       => $this->getBedCount($bed_num), // 有几张床
            'bed_size'      => $bed_size, // 床有多大
            'total_num'     => 10,// 房量
            'windows'       => $this->getWindows($windows), //是否有窗户
            'network'       => strtoupper($network),//  WIFI网络
            'xiyan'         => strpos($xiyan, '允许') !== false ? '允许' : '禁止', // 吸烟政策
            'breakfast'     => $this->getBreakfastCount($breakfast),// 免费早餐
            'yj_cost'       => 0, // 押金金额
            'wifi_network'  => !empty($property_info['wifi_network']) ? $property_info['wifi_network'] : '[]', // 网络通讯
            'kefang_buju'   => !empty($property_info['kefang_buju']) ? $property_info['kefang_buju'] : '[]', // 客房布局
            'xiyu_yongpin'  => !empty($property_info['xiyu_yongpin']) ? $property_info['xiyu_yongpin'] : '[]', // 洗浴用品
            'kefang_sheshi' => !empty($property_info['kefang_sheshi']) ? $property_info['kefang_sheshi'] : '[]', // 客房设施
            'shipin_yinpin' => !empty($property_info['shipin_yinpin']) ? $property_info['shipin_yinpin'] : '[]', // 食品饮品
            'meiti_keji'    => !empty($property_info['meiti_keji']) ? $property_info['meiti_keji'] : '[]', // 媒体科技
            'qingjie_fuwu'  => !empty($property_info['qingjie_fuwu']) ? $property_info['qingjie_fuwu'] : '[]', // 清洁服务
            'bianli_sheshi' => !empty($property_info['bianli_sheshi']) ? $property_info['bianli_sheshi'] : '[]', // 便利设施

        ];
        $room    = Room::create($insdata);
        $room_id = $room->id;
        // 礼包
        $ins_skudata = [];
        $createdata  = [];
        foreach ($room_sku_list as &$item) {
            $item['hotel_id']      = Admin::user()->hotel_id;
            $item['room_id']       = $room_id;
            $item['sku_code']      = rand(100000, 999999);
            $item['roomsku_stock'] = 10;
            $item['roomsku_tags']  = json_encode($this->getRoomskuTags($item['roomsku_tags']), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $item['roomsku_gift']  = json_encode($this->getRoomskuGift($item['roomsku_gift']), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $where                 = [
                'hotel_id'       => Admin::user()->hotel_id,
                'roomsku_zaocan' => $item['roomsku_zaocan'],
                'roomsku_gift'   => json_encode($item['roomsku_gift']),
                'room_id'        => $room_id,
                'roomsku_price'  => $item['roomsku_price'],
            ];
            $createdata[]          = $item;
            RoomSkuPrice::firstOrCreate($where, $item);
        }
        echo "<pre>";
        print_r($createdata);
        echo "</pre>";
        exit;
    }

    public function getRoomskuGift($roomsku_gift) {
        if (empty($roomsku_gift[0]['gift_package_name'])) {
            return [];
        }
        $gift_ids = [];
        foreach ($roomsku_gift as $items) {
            $where       = ['hotel_id' => Admin::user()->hotel_id, 'sku_gift_name' => $items['gift_package_name']];
            $create_data = [
                'hotel_id'       => Admin::user()->hotel_id,
                'sku_gift_name'  => $items['gift_package_name'],
                'sku_gift_img'   => 'https://rb-booking.oss-cn-guangzhou.aliyuncs.com/images/3ee3128c9ee2e472bb3d7f721f4d0551.jpg',
                'sku_gift_brief' => $items['gift_package_name'],
                'sku_gift_desc'  => $items['gift_package_description'],
                'sku_gift_price' => $items['pkgSalePrice'],
                'sku_gift_sorts' => 50,
            ];
            $gift_model  = RoomSkuGift::firstOrCreate($where, $create_data);
            $gift_ids[]  = $gift_model->id;
        }
        return $gift_ids;
    }

    // 获取房间sku tag 的ID,
    public function getRoomskuTags(array $roomsku_tags) {
        if (empty($roomsku_tags[0]['productLabelName'])) {
            return [];
        }
        $roomsku_tags_arr = [];
        foreach ($roomsku_tags as $items) {
            $roomsku_tags_arr[] = $items['productLabelName'];
        }

        $ids                 = RoomSkuTag::where(['hotel_id' => Admin::user()->hotel_id])->whereIn('sku_tags_name', $roomsku_tags_arr)->get('id');
        $oneDimensionalArray = [];
        if ($ids) {
            $oneDimensionalArray = array_column($ids->toArray(), 'id');
        }
        return $oneDimensionalArray;
    }

    // 多线程下载图片
    public function downloadImages($imgarr) {
        try {
            $mh      = curl_multi_init();
            $handles = [];

            foreach ($imgarr as $i => $itemss) {
                $ch       = curl_init();
                $filename = $itemss['local_save_path'];
                $fp       = fopen($filename, 'w');
                curl_setopt($ch, CURLOPT_URL, $itemss['res_url']);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                curl_setopt($ch, CURLOPT_REFERER, $itemss['res_url']);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                $handles[$i] = ['ch' => $ch, 'fp' => $fp];

                curl_multi_add_handle($mh, $ch);
            }


            $running = null;
            $i       = 1;
            do {
                /*if($i >= 100000){ //防止死循环
                    break;
                }*/
                curl_multi_exec($mh, $running);
                $i++;
            } while ($running > 0);

            foreach ($handles as $handle) {
                curl_multi_remove_handle($mh, $handle['ch']);
                curl_close($handle['ch']);
                fclose($handle['fp']);
            }
            curl_multi_close($mh);
        } catch (\Error $error) {

        } catch (\Exception $exception) {

        }
    }

    public function extractImageUrlsFromJson($jsonString) {
        // 更新正则表达式模式，支持 http, https 和 // 开头的 URL
        $pattern = '/(?:https?:)?\/\/[^\s"]+\.(?:jpg|jpeg|png|gif|webp)/i';

        // 使用 preg_match_all 函数查找所有匹配的 URL
        preg_match_all($pattern, $jsonString, $matches);
        if (!empty($matches[0])) {
            return array_flip(array_flip($matches[0]));
        }
        // 返回匹配的 URL 数组
        return $matches[0];
    }
}
