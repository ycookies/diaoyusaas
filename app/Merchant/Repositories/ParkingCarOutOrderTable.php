<?php

namespace App\Merchant\Repositories;

use Dcat\Admin\Repositories\Repository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Dcat\Admin\Grid;
use App\Services\ParkingService;

class ParkingCarOutOrderTable extends Repository
{
    /**
     * Get the grid data.
     *
     * @param Grid\Model $model
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|Collection|array
     */
    public function get(Grid\Model $model)
    {
        $page     = $model->getCurrentPage();
        $pageSize = $model->getPerPage();
        $search = $model->filter()->input('_search_');
        $res = $this->all($search);
        return $model->makePaginator(
            $res['recordCount'],
            $res['recordList']
        );
    }

    protected function all($search)
    {
        $hotel_id = \Dcat\Admin\Admin::user()->hotel_id;
        $service = new ParkingService($hotel_id);
        $data = [
            'recordList' => [],
            'recordCount'=> 0
        ];
        if(empty($search)){
            return $data;
        }

        $postdata = [
            'carNo' => $search,
        ];
        $res = $service->sendapi('yunpark/thirdInterface/getCarFee',$postdata);
        $res = json_decode($res,true);
        if(empty($res['result']['recordList'])){
            return $data;
        }
        $data = [
            'recordList' => $res['result']['recordList'],
            'recordCount'=> $res['result']['recordCount']
        ];
        return $data;
    }

}
