<?php

namespace App\Admin\Actions\Tree;

use Dcat\Admin\Tree\RowAction;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Traits\HasPermissions;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Dcat\Admin\Widgets\Form as WidgetsForm;
use App\Models\Cgcms\Arctype as ArctypeModel;

class ViewArctypeList extends RowAction
{
    /**
     * @return string
     */
    protected $title = 'Title';

    /**
     * Handle the action request.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request)
    {
        $typeid = $this->getKey();
        $info = ArctypeModel::find($typeid);
        $url = '/archive?typeid='.$typeid;
        /*if($info->current_channel == 6){
            $url = '';
        }*/
        return $this->response()->redirect($url);
    }

    /**
     * 重写title方法
     * @desc
     * @return string|void
     * author eRic
     * dateTime 2022-12-24 16:42
     */
    public function title() {
        $icon = 'icon-eye'; //$this->getRow()->show ? 'icon-eye-off' : 'icon-eye';

        return "&nbsp;<i class='feather $icon'></i>&nbsp;";
    }

    /**
     * @return string|void
     */
    protected function href()
    {
        // return admin_url('auth/users');
    }

    /**
     * @return string|array|void
     */
    public function confirm()
    {
        // return ['Confirm?', 'contents'];
    }

    /**
     * @param Model|Authenticatable|HasPermissions|null $user
     *
     * @return bool
     */
    protected function authorize($user): bool
    {
        return true;
    }
}
