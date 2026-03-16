<?php

namespace App\Merchant\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Dcat\Admin\Traits\HasPermissions;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Dcat\Admin\Traits\ModelTree;

/**
 * Class Administrator.
 *
 * @property Role[] $roles
 */
class Administrator extends Model implements AuthenticatableContract
{
    use ModelTree;

    use Authenticatable,
        HasPermissions,
        HasDateTimeFormatter;
    const DEFAULT_ID = 1;
    public $table  = 'merchant_users';
    //protected $fillable = ['username', 'password', 'name', 'avatar'];
    protected  $guarded = ['password'];
    protected $titleColumn = 'username';

    protected $orderColumn = 'sort';

    protected $parentColumn = 'parent_id';
    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->init();

        parent::__construct($attributes);
    }

    protected function init()
    {
        $connection = config('merchant.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('merchant.database.users_table'));
    }

    /**
     * Get avatar attribute.
     *
     * @return mixed|string
     */
    public function getAvatar()
    {
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
    public function roles(): BelongsToMany
    {
        $pivotTable = config('merchant.database.role_users_table');

        $relatedModel = config('merchant.database.roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'role_id')->withTimestamps();
    }

    /**
     * 判断是否允许查看菜单.
     *
     * @param  array|Menu  $menu
     * @return bool
     */
    public function canSeeMenu($menu)
    {
        return true;
    }

    public function getSaaSVersionInfo()
    {
        $saas_version_name = '酒店五星级版';
        $saas_version_expired_at = $this->expired_at;
        if($this->parent_id != 0){
            $info = MerchantUser::where('id',$this->parent_id)->first();
            $saas_version_expired_at = $info->expired_at;
        }
        
        return [
            'saas_version_name' => $saas_version_name,
            'saas_version_expired_at' => $saas_version_expired_at,
        ];
    }
}
