<?php

namespace App\Api\Hotel;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Hotel\Seller;
use Orion\Http\Controllers\Controller;
use Orion\Concerns\DisablePagination;
use Illuminate\Support\Facades\Auth;
use Orion\Concerns\DisableAuthorization;
use Illuminate\Pagination\LengthAwarePaginator;

class BaseController extends Controller
{

    // 禁止授权访问
    use DisableAuthorization;

    public $user;
    public $seller;
    public $hotel;
    public $hotel_id;
    public function __construct() {
        if(Request()->has('hotel_id')){
            $hotelinfo = Seller::find(Request()->get('hotel_id'));
            $this->hotel = $hotelinfo;
            $this->hotel_id = $hotelinfo->id;
        }
        $this->seller = [
            'seller_id' => 1,
        ];
    }

    /**
     * @desc 封装分页展示数据
     * author eRic
     * $Resource 过滤后的数据
     * $list_total 记录总数
     * dateTime 2021-12-15 18:02
     */
    public function pageintes($list,$pagesize = 20,$Resource = null,$list_total = ''){
        $page = 1;
        $total = 0;
        if($list instanceof LengthAwarePaginator){
            $items = $list->items();
            $total = $list->total();
            $page = $list->currentPage();
        }else{
            $items = $list;
            $total = count(collect($list)->toArray());
        }
        if(!empty($Resource)){
            $items = $Resource;
        }
        if(!empty($list_total)){
            $total =   $list_total;
        }
        $data['list'] = $items;
        $data['page_info'] = [
            'pagesize' => (int)$pagesize,
            'page' => $page,
            'total' => $total,
        ];
        return $data;
    }

}
