<?php

namespace App\Services;

use App\Models\Hotel\Room;
use App\Models\Hotel\Roomprice;
use App\Models\Hotel\RoomSkuPrice;
use App\Models\Hotel\RoomTiaojiaLog;

/**
 * 房型客房销售SKU调价服务
 * @package App\Services
 * anthor Fox
 */
class RoomSkuTiaojiaService extends BaseService {
    const Batch_tiaojia_type_arr = [
        '0' => '自宝义',
        '1' => '周末',
        '2' => '春节',
        '3' => '元宵节',
        '4' => '2.14情人节',
        '5' => '51劳动节',
        '6' => '中秋节',
        '7' => '国庆节',
        '8' => '元旦节',
        '9' => '圣诞节',
        '10' => '38妇女节',
        '11' => '端午节',
        '12' => '清明节',
        '13' => '重阳节',
    ];
    const Set_price_type = [
        1 => '加价',
        2 => '减价',
        3 => '上调百分比',
        4 => '下调百分比'
    ];

    // 执行调价
    public function exeTiaojia($id) {
        $info   = RoomTiaojiaLog::find($id);
        $status = $this->handle($info->date_type, $info);

        if ($status !== false) {
            // 更新执行情况
            $info->status = 1;
            $info->save();
        }
        return true;
    }

    // 清除批量调价
    public static function removeTiaojia($tiaojia_logid) {
        $info = Roomprice::where(['tiaojia_logid' => $tiaojia_logid])->delete();
        RoomTiaojiaLog::where(['id' => $tiaojia_logid])->delete();
        return true;
    }

    /**
     * 事件引导处理方法
     */
    protected function handle($batch_tiaojia_type, $info) {
        $method = 'tiaojia_type_' . $batch_tiaojia_type;
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], [$info]);
        } else {
            return false;
        }
    }

    // 计算调价后的金额

    /**
     * bcadd — 加法
     * bccomp — 比较
     * bcdiv — 相除
     * bcmod — 求余数
     * bcmul — 乘法
     * bcpow — 次方
     * bcpowmod — 先次方然后求余数
     * bcscale — 给所有函数设置小数位精度
     * bcsqrt — 求平方根
     * bcsub — 减法
     */
    public function jisuanPrice($room_price, $set_price, $set_value) {
        $new_price = false;
        switch ($set_price) {
            case 1 :
                $new_price = bcadd($room_price, $set_value, 2);
                break;
            case 2 :
                $new_price = bcsub($room_price, $set_value, 2);
                break;
            case 3 :
                $ra        = bcmul($room_price, ($set_value * 0.01), 2);
                $new_price = bcadd($room_price, $ra, 2);
                break;
            case 4 :
                $ra        = bcmul($room_price, ($set_value * 0.01), 2);
                $new_price = bcsub($room_price, $ra, 2);
                break;
            default:
                break;
        }
        if ($new_price <= 0) {
            return false;
        }
        return formatFloats($new_price);
    }

    // 自定义日期范围
    public function tiaojia_type_0($info) {
        $start_date    = $info->start_date;
        $end_date      = $info->end_date;
        $room_sku_ids  = $info->room_sku_ids;
        $set_price     = $info->set_price; // 调价方式
        $set_value     = $info->set_value; // 调价数值
        $room_sku_list = RoomSkuPrice::whereIn('id', json_decode($room_sku_ids, true))->get();
        $datelist      = getDatesInRange($start_date, $end_date);
        foreach ($room_sku_list as $key => $item) {
            $room_price = $this->jisuanPrice($item->roomsku_price, $set_price, $set_value);
            if ($room_price !== false) {
                foreach ($datelist as $datel) {
                    Roomprice::addSkuDaysPice($item->hotel_id, $item->room_id, $item->id, 'online_price', $datel, $room_price, 1, 'batch', $info->date_type, $info->id);
                }
            }

        }
        return true;
    }

    // 周末
    public function tiaojia_type_1($info) {
        $start_date = date('Y-m-d', strtotime('-10 days'));
        $end_date = date('Y-m-d', strtotime('+90 days'));

        $room_sku_ids  = $info->room_sku_ids;
        $set_price  = $info->set_price; // 调价方式
        $set_value  = $info->set_value; // 调价数值

        $room_list = RoomSkuPrice::whereIn('id', json_decode($room_sku_ids, true))->get();
        $datelist  = getWeekendDatesInRange($start_date, $end_date);
        foreach ($room_list as $key => $item) {
            $room_price = $this->jisuanPrice($item->roomsku_price, $set_price, $set_value);
            if ($room_price !== false) {
                foreach ($datelist as $datel) {
                    Roomprice::addSkuDaysPice($item->hotel_id, $item->room_id, $item->id, 'online_price', $datel, $room_price, 1, 'batch', $info->date_type, $info->id);
                }
            }
        }
        return true;
    }

    // 春节
    public function tiaojia_type_2($info) {
        $start_date = '2025-01-29';
        $end_date   = '2025-02-04';
        $room_sku_ids  = $info->room_sku_ids;
        $set_price  = $info->set_price; // 调价方式
        $set_value  = $info->set_value; // 调价数值

        $room_list = RoomSkuPrice::whereIn('id', json_decode($room_sku_ids, true))->get();
        $datelist  = getDatesInRange($start_date, $end_date);
        foreach ($room_list as $key => $item) {
            $room_price = $this->jisuanPrice($item->roomsku_price, $set_price, $set_value);
            if ($room_price !== false) {
                foreach ($datelist as $datel) {
                    Roomprice::addSkuDaysPice($item->hotel_id, $item->room_id, $item->id, 'online_price', $datel, $room_price, 1, 'batch', $info->date_type, $info->id);
                }
            }
        }
        return true;
    }

    // 元宵节
    public function tiaojia_type_3($info) {
        $start_date = '2025-02-12';
        $end_date   = '2025-02-12';
        $room_sku_ids  = $info->room_sku_ids;
        $set_price  = $info->set_price; // 调价方式
        $set_value  = $info->set_value; // 调价数值

        $room_list = RoomSkuPrice::whereIn('id', json_decode($room_sku_ids, true))->get();
        $datelist  = getDatesInRange($start_date, $end_date);
        foreach ($room_list as $key => $item) {
            $room_price = $this->jisuanPrice($item->roomsku_price, $set_price, $set_value);
            if ($room_price !== false) {
                foreach ($datelist as $datel) {
                    Roomprice::addSkuDaysPice($item->hotel_id, $item->room_id, $item->id, 'online_price', $datel, $room_price, 1, 'batch', $info->date_type, $info->id);
                }
            }
        }
        return true;
    }


    // 2.14情人节
    public function tiaojia_type_4($info) {
        $start_date = date('Y').'-02-14';
        $end_date   = date('Y').'-02-14';
        $room_sku_ids  = $info->room_sku_ids;
        $set_price  = $info->set_price; // 调价方式
        $set_value  = $info->set_value; // 调价数值

        $room_list = RoomSkuPrice::whereIn('id', json_decode($room_sku_ids, true))->get();
        $datelist  = getDatesInRange($start_date, $end_date);
        foreach ($room_list as $key => $item) {
            $room_price = $this->jisuanPrice($item->roomsku_price, $set_price, $set_value);
            if ($room_price !== false) {
                foreach ($datelist as $datel) {
                    Roomprice::addSkuDaysPice($item->hotel_id, $item->room_id, $item->id, 'online_price', $datel, $room_price, 1, 'batch', $info->date_type, $info->id);
                }
            }
        }
        return true;
    }

    // 51 劳动节
    public function tiaojia_type_5($info) {
        $start_date = date('Y').'-05-01';
        $end_date   = date('Y').'-05-05';
        $room_sku_ids  = $info->room_sku_ids;
        $set_price  = $info->set_price; // 调价方式
        $set_value  = $info->set_value; // 调价数值

        $room_list = RoomSkuPrice::whereIn('id', json_decode($room_sku_ids, true))->get();
        $datelist  = getDatesInRange($start_date, $end_date);
        foreach ($room_list as $key => $item) {
            $room_price = $this->jisuanPrice($item->roomsku_price, $set_price, $set_value);
            if ($room_price !== false) {
                foreach ($datelist as $datel) {
                    Roomprice::addSkuDaysPice($item->hotel_id, $item->room_id, $item->id, 'online_price', $datel, $room_price, 1, 'batch', $info->date_type, $info->id);
                }
            }
        }
        return true;
    }

    // 中秋节
    public function tiaojia_type_6($info) {
        $festivalMap = [
            2025 => '2025-10-06',
            2026 => '2026-09-25',
            2027 => '2027-09-15',
            2028 => '2028-10-03',
            2029 => '2029-09-22',
            2030 => '2030-09-12',
        ];
        $start_date = $festivalMap(date('Y'));
        $end_date   = $start_date;
        $room_sku_ids  = $info->room_sku_ids;
        $set_price  = $info->set_price; // 调价方式
        $set_value  = $info->set_value; // 调价数值

        $room_list = RoomSkuPrice::whereIn('id', json_decode($room_sku_ids, true))->get();
        $datelist  = getDatesInRange($start_date, $end_date);
        foreach ($room_list as $key => $item) {
            $room_price = $this->jisuanPrice($item->roomsku_price, $set_price, $set_value);
            if ($room_price !== false) {
                foreach ($datelist as $datel) {
                    Roomprice::addSkuDaysPice($item->hotel_id, $item->room_id, $item->id, 'online_price', $datel, $room_price, 1, 'batch', $info->date_type, $info->id);
                }
            }
        }
        return true;
    }

    // 国庆节
    public function tiaojia_type_7($info) {
        $start_date = date('Y').'-10-01';
        $end_date   = date('Y').'-10-05';
        $room_sku_ids  = $info->room_sku_ids;
        $set_price  = $info->set_price; // 调价方式
        $set_value  = $info->set_value; // 调价数值

        $room_list = RoomSkuPrice::whereIn('id', json_decode($room_sku_ids, true))->get();
        $datelist  = getDatesInRange($start_date, $end_date);
        foreach ($room_list as $key => $item) {
            $room_price = $this->jisuanPrice($item->roomsku_price, $set_price, $set_value);
            if ($room_price !== false) {
                foreach ($datelist as $datel) {
                    Roomprice::addSkuDaysPice($item->hotel_id, $item->room_id, $item->id, 'online_price', $datel, $room_price, 1, 'batch', $info->date_type, $info->id);
                }
            }
        }
        return true;
    }

    // 元旦节
    public function tiaojia_type_8($info) {
        $start_date = (date('Y')+1).'-01-01';
        $end_date   = (date('Y')+1).'-01-01';
        $room_sku_ids  = $info->room_sku_ids;
        $set_price  = $info->set_price; // 调价方式
        $set_value  = $info->set_value; // 调价数值

        $room_list = RoomSkuPrice::whereIn('id', json_decode($room_sku_ids, true))->get();
        $datelist  = getDatesInRange($start_date, $end_date);
        foreach ($room_list as $key => $item) {
            $room_price = $this->jisuanPrice($item->roomsku_price, $set_price, $set_value);
            if ($room_price !== false) {
                foreach ($datelist as $datel) {
                    Roomprice::addSkuDaysPice($item->hotel_id, $item->room_id, $item->id, 'online_price', $datel, $room_price, 1, 'batch', $info->date_type, $info->id);
                }
            }
        }
        return true;
    }

    // 圣诞节
    public function tiaojia_type_9($info) {
        $start_date = date('Y').'-12-25';
        $end_date   = date('Y').'-12-25';
        $room_sku_ids  = $info->room_sku_ids;
        $set_price  = $info->set_price; // 调价方式
        $set_value  = $info->set_value; // 调价数值

        $room_list = RoomSkuPrice::whereIn('id', json_decode($room_sku_ids, true))->get();
        $datelist  = getDatesInRange($start_date, $end_date);
        foreach ($room_list as $key => $item) {
            $room_price = $this->jisuanPrice($item->price, $set_price, $set_value);
            if ($room_price !== false) {
                foreach ($datelist as $datel) {
                    Roomprice::addDaysPice($item->hotel_id, $item->id, 'online_price', $datel, $room_price, 1, 'batch', $info->date_type, $info->id);
                }
            }
        }
        return true;
    }

    // 38妇女节
    public function tiaojia_type_10($info) {
        $start_date = date('Y').'-03-08';
        $end_date   = date('Y').'-03-08';
        $room_sku_ids  = $info->room_sku_ids;
        $set_price  = $info->set_price; // 调价方式
        $set_value  = $info->set_value; // 调价数值

        $room_list = RoomSkuPrice::whereIn('id', json_decode($room_sku_ids, true))->get();
        $datelist  = getDatesInRange($start_date, $end_date);
        foreach ($room_list as $key => $item) {
            $room_price = $this->jisuanPrice($item->price, $set_price, $set_value);
            if ($room_price !== false) {
                foreach ($datelist as $datel) {
                    Roomprice::addDaysPice($item->hotel_id, $item->id, 'online_price', $datel, $room_price, 1, 'batch', $info->date_type, $info->id);
                }
            }
        }
        return true;
    }


    // 端午节
    public function tiaojia_type_11($info) {
        $festivalMap = [
            2025 => '2025-05-31',
            2026 => '2026-06-19',
            2027 => '2027-06-09',
            2028 => '2028-05-28',
            2029 => '2029-06-16',
            2030 => '2030-06-05',
        ];
        $start_date = $festivalMap[date('Y')];
        $end_date   = \Carbon\Carbon::parse($start_date)->addDays(2)->format('Y-m-d');;
        $room_sku_ids  = $info->room_sku_ids;
        $set_price  = $info->set_price; // 调价方式
        $set_value  = $info->set_value; // 调价数值

        $room_list = RoomSkuPrice::whereIn('id', json_decode($room_sku_ids, true))->get();
        $datelist  = getDatesInRange($start_date, $end_date);
        foreach ($room_list as $key => $item) {
            $room_price = $this->jisuanPrice($item->price, $set_price, $set_value);
            if ($room_price !== false) {
                foreach ($datelist as $datel) {
                    Roomprice::addDaysPice($item->hotel_id, $item->id, 'online_price', $datel, $room_price, 1, 'batch', $info->date_type, $info->id);
                }
            }
        }
        return true;
    }

    // 清明节
    public function tiaojia_type_12($info) {
        $start_date = date('Y').'-04-04';
        $end_date   = date('Y').'-04-05';
        $room_sku_ids  = $info->room_sku_ids;
        $set_price  = $info->set_price; // 调价方式
        $set_value  = $info->set_value; // 调价数值

        $room_list = RoomSkuPrice::whereIn('id', json_decode($room_sku_ids, true))->get();
        $datelist  = getDatesInRange($start_date, $end_date);
        foreach ($room_list as $key => $item) {
            $room_price = $this->jisuanPrice($item->price, $set_price, $set_value);
            if ($room_price !== false) {
                foreach ($datelist as $datel) {
                    Roomprice::addDaysPice($item->hotel_id, $item->id, 'online_price', $datel, $room_price, 1, 'batch', $info->date_type, $info->id);
                }
            }
        }
        return true;
    }

    // 重阳节
    public function tiaojia_type_13($info) {
        $festivalMap = [
            2025 => '2025-10-30',
            2026 => '2026-10-20',
            2027 => '2027-10-09',
            2028 => '2028-10-28',
            2029 => '2029-10-17',
            2030 => '2030-10-06',
        ];
        $start_date = $festivalMap[date('Y')];
        $end_date   = $start_date;
        $room_sku_ids  = $info->room_sku_ids;
        $set_price  = $info->set_price; // 调价方式
        $set_value  = $info->set_value; // 调价数值

        $room_list = RoomSkuPrice::whereIn('id', json_decode($room_sku_ids, true))->get();
        $datelist  = getDatesInRange($start_date, $end_date);
        foreach ($room_list as $key => $item) {
            $room_price = $this->jisuanPrice($item->price, $set_price, $set_value);
            if ($room_price !== false) {
                foreach ($datelist as $datel) {
                    Roomprice::addDaysPice($item->hotel_id, $item->id, 'online_price', $datel, $room_price, 1, 'batch', $info->date_type, $info->id);
                }
            }
        }
        return true;
    }
}