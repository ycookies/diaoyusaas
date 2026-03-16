<?php

namespace App\Merchant\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Dcat\Admin\Traits\HasPermissions;
use Dcat\Admin\Traits\ModelTree;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

/**
 * Class Administrator.
 *
 * @property Role[] $roles
 */
class MerchantUser extends Model implements AuthenticatableContract {
    use ModelTree;
    use Authenticatable,
        HasPermissions,
        HasDateTimeFormatter;

    const DEFAULT_ID = 1;
    const Permissions_arr = [
        'booking'           => '订房系统',
        'hotel_map'         => '酒店导航',
        'wx_gzh_manage'     => '公众号授权管理',
        'minapp_tpl_manage' => '小程序模板管理',
        'profitsharing'     => '订房交易分账',
        'wx_card'           => '小程序会员卡',
        'vip_member'        => '付费会员',
        'coupon'            => '优惠券',
        'kefu'              => '客服管理',
        'share'             => '一级分销推荐',
        'store_value'       => '储值',
        'points'            => '积分',
    ];
    protected $table = 'merchant_users';
    protected $fillable = ['username', 'password', 'parent_id', 'name', 'avatar', 'email', 'email_verified', 'phone', 'api_token', 'user_status', 'balance', 'point','expired_at','module_permissions','is_show_copyright'];

    protected $hidden = [
        'password', 'remember_token',
    ];
    protected $titleColumn = 'username';

    protected $orderColumn = 'sort';

    protected $parentColumn = 'parent_id';

    const Work_status = [
        1 => '在线',
        2 => '休息',
    ];
    protected $appends = ['is_wx_openid'];

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array $attributes
     */
    public function __construct(array $attributes = []) {
        $this->init();

        parent::__construct($attributes);
    }

    public function setModulePermissionsAttribute($module_permissions) {
        if (is_array($module_permissions)) {
            $this->attributes['module_permissions'] = json_encode($module_permissions,JSON_UNESCAPED_UNICODE);
        }
    }

    protected function init() {
        $connection = config('merchant.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('merchant.database.users_table'));
    }

    /**
     * Get avatar attribute.
     *
     * @return mixed|string
     */
    public function getAvatar() {
        $avatar = $this->avatar;

        if ($avatar) {
            if (!URL::isValidUrl($avatar)) {
                $avatar = Storage::disk(config('merchant.upload.disk'))->url($avatar);
            }

            return $avatar;
        }

        return admin_asset(config('merchant.default_avatar') ?: '@admin/images/default-avatar.jpg');
    }

    /**
     * A user has and belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany {
        $pivotTable = config('merchant.database.role_users_table');

        $relatedModel = config('merchant.database.roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'role_id')->withTimestamps();
    }

    /**
     * 判断是否允许查看菜单.
     *
     * @param  array|Menu $menu
     * @return bool
     */
    public function canSeeMenu($menu) {
        return true;
    }

    // 增加余额
    static public function addBalance($user_id, $money) {
        return self::where('id', $user_id)->increment('balance', $money);
    }

    // 减少余额
    static public function cutBalance($user_id, $money) {
        return self::where('id', $user_id)->decrement('balance', $money);
    }

    // 绑定微信ID
    static public function bindwxOpenid($user_id, $openid) {
        return self::where('id', $user_id)->update(['wx_openid' => $openid]);
    }

    // 增加一个字段

    /**
     * @desc 转换输出
     */
    public function getIsWxOpenidAttribute() {
        if (!empty($this->wx_openid)) {
            return 1;
        }
        return 0;
    }

    public function hotel() {
        return $this->hasOne(\App\Models\Hotel\Seller::class, 'id', 'hotel_id')->select('id', 'name', 'ewm_logo');
    }

}
