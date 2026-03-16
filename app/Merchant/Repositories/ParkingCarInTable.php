<?php

namespace App\Merchant\Repositories;

use Dcat\Admin\Repositories\Repository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Dcat\Admin\Grid;
use Dcat\Admin\Admin;
use App\Services\ParkingService;

class ParkingCarInTable extends Repository
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

        $res = $this->all($page,$pageSize);

        return $model->makePaginator(
            $res['recordCount'],
            $res['recordList']
        );
    }

    protected function all($page,$pageSize)
    {
        $hotel_id = Admin::user()->hotel_id;
        $service = new ParkingService($hotel_id);
        $data = [
            'pageNum' => $page,
            'pageSize' => $pageSize,
            'startTime' => date('Y-m-d H:i:s',strtotime('-30 days')),
            'endTime' => date('Y-m-d 23:59:59'),
        ];
        $res = $service->sendapi('yunpark/thirdInterface/getCarIn',$data);
        $res = json_decode($res,true);
        $data = [
            'recordList' => [],
            'recordCount'=> 0
        ];
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
