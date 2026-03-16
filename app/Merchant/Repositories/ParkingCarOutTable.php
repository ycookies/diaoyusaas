<?php

namespace App\Merchant\Repositories;

use Dcat\Admin\Repositories\Repository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Dcat\Admin\Grid;
use App\Services\ParkingService;
class ParkingCarOutTable extends Repository
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

        $collection = $this->all()->forPage($page, $pageSize);

        return $model->makePaginator(
            $this->all()->count(),
            $collection
        );
    }

    protected function all()
    {
        $hotel_id = \Dcat\Admin\Admin::user()->hotel_id;
        $service = new ParkingService($hotel_id);
        $data = [
            'pageNum' => 1,
            'pageSize' => 10,
            'startTime' => date('Y-m-d H:i:s',strtotime('-30 days')),
            'endTime' => date('Y-m-d 23:59:59'),
        ];
        $res = $service->sendapi('yunpark/thirdInterface/getCarOut',$data);
        $res = json_decode($res,true);
        if(empty($res['result']['recordList'])){
            return collect([]);
        }
        return collect($res['result']['recordList']);
    }

}
