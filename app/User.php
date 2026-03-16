<?php

namespace App;

use App\Models\Hotel\UserLevel;
use App\Models\Hotel\UserRongbaopayOpenid;
use App\Services\UserWxCardService;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\Hotel\IntegralLog;
use App\Models\Hotel\BalanceLog;

class User extends Authenticatable implements JWTSubject {
    use Notifiable;

    protected $connection = 'hotel';
    protected $table = 'user';
    protected $guarded = [];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    /*protected $fillable = [
        'name', 'email', 'password','api_token',
    ];*/

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token', 'updated_at', 'session_key',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $pay_type_arr = [
        'wx'     => 'wx_openid',
        'alipay' => 'alipay_openid',
        'bank'   => 'bankpay_openid',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }

    /**
     * @desc 绑定融宝支付的openid
     * @param $hotel_id 酒店ID
     * @param $pay_type 支付渠道类型
     * @param $openid 支付用户openid
     * @param bool $up_force 是否强制更新
     * @return bool
     * author eRic
     * dateTime 2024-05-16 10:29
     */
    public function saveRongbaopayOpenid($hotel_id, $pay_type, $openid,$up_force = false) {
        if (empty($this->pay_type_arr[$pay_type])) {
            return false;
        }
        $field = $this->pay_type_arr[$pay_type];
        $info  = UserRongbaopayOpenid::where(['user_id' => $this->id, 'hotel_id' => $hotel_id])->first();
        if (!empty($info->id)) {
            if (!empty($info->$field)) {
                return false;
            }
            return UserRongbaopayOpenid::where(['user_id' => $this->id, 'hotel_id' => $hotel_id])->update([$field => $openid]);
        }
        $insdata = [
            'user_id'  => $this->id,
            'hotel_id' => $hotel_id,
            $field     => $openid,
        ];
        return UserRongbaopayOpenid::create($insdata);
    }

    // 获取绑定融宝支付的所有openid
    public function getRongbaopayOpenid($hotel_id,$pay_type = 'wx') {
        if (empty($this->pay_type_arr[$pay_type])) {
            return '';
        }
        $field = $this->pay_type_arr[$pay_type];
        $info =  UserRongbaopayOpenid::where(['user_id' => $this->id, 'hotel_id' => $hotel_id])->first();
        if(!empty($info->$field)){
            return $info->$field;
        }
        return '';
    }

    // 增加订房次数
    public static function addBookingNum($user_id) {
        $res = self::where(['id' => $user_id])->increment('booking_num');
        self::sysAutoUpLevel($user_id); // 检查是否符合条件自动提升等级
        return $res;
    }

    // 检查是否符合条件自动提升等级
    public static  function sysAutoUpLevel($user_id){
        $res = self::where(['id' => $user_id])->first();
        $booking_num = $res->booking_num;
        $level_num = $res->user_level;
        $level_id = $res->level_id;

        $level_info = UserLevel::where(['hotel_id'=> $res->hotel_id,'level_num'=> ($level_num + 1)])->first();
        // 订房次数 大于了设置的值
        if($booking_num >= $level_info->min_booking_num){
            self::uplevel($user_id,$level_info->id,'');
            return true;
        }
        return false;
    }

    // 增加余额
    public static function addBalance($user_id, $money, $desc) {
        $res = self::where(['id' => $user_id])->increment('balance', $money);

        BalanceLog::addLog($user_id, $money, $desc);
        // 更新会员卡
        (new UserWxCardService())->addBalance($user_id, $money, $desc);
        return true;
    }

    // 支出余额
    public static function cutBalance($user_id, $cutmoney, $desc) {
        $res = self::where(['id' => $user_id])->decrement('balance', $cutmoney);

        BalanceLog::cutLog($user_id, $cutmoney, $desc);

        // 更新会员卡
        (new UserWxCardService())->cutBalance($user_id, $cutmoney, $desc);
        return true;
    }

    // 增加积分
    public static function addPoint($user_id, $point_num, $desc) {
        $res = self::where(['id' => $user_id])->increment('point', $point_num);
        IntegralLog::addLog($user_id, $point_num, $desc);
        // 更新会员卡
        (new UserWxCardService())->addPoint($user_id, $point_num, $desc);
        return true;
    }


    // 支出积分
    public static function cutPoint($user_id, $cut_point, $desc) {
        $res = self::where(['id' => $user_id])->decrement('point', $cut_point);
        IntegralLog::cutLog($user_id, $cut_point, $desc);
        // 更新会员卡
        (new UserWxCardService())->cutPoint($user_id, $cut_point, $desc);
        return true;
    }

    //  更新会员等级
    public static function uplevel($user_id, $level_id, $desc) {
        $info = \App\Models\Hotel\UserLevel::where(['id' => $level_id])->first();
        $updata = [
            'user_level' => $info->level_num,
            'level_id'=> $level_id,
        ];
        $res = self::where(['id' => $user_id])->update($updata);

        // 更新会员卡
        //(new UserWxCardService())->uplevel($user_id, $info->level_name, $desc);
        return true;
    }


    //  更新用户超级vip
    public static function upVip($user_id, $viporder) {
        //$info = \App\Models\Hotel\MemberVipSet::where(['id' => $vip_id])->first();
        $updata = [
            'vipId'     => $viporder->vipId,
            'vipExpire' => $viporder->vipExpire,
        ];
        $res = self::where(['id' => $user_id])->update($updata);

        // 更新会员卡
        //(new UserWxCardService())->uplevel($user_id, $info->level_name, $desc);
        return true;
    }
}
