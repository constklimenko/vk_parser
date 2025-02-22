<?php

namespace App\Http\Controllers;

use App\ClickHouseModels\VkBannerStat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BannerStatisticsController extends Controller
{
    public function get( Request $request ) {
        $arrRequest = $request->all();
        $filter = [];
        $order = (!empty($arrRequest['order'])) ?$arrRequest['order']:'desc';
        $sort = (!empty($arrRequest['sort'])) ?[$arrRequest['sort'] => $order]: ['banner_id' => $order];
        $limit = (!empty($arrRequest['limit'])) ? $arrRequest['limit'] : 10;
        $offset = (!empty($arrRequest['offset'])) ? $arrRequest['offset'] : 0;
        $group = (!empty($arrRequest['group'])) ? $arrRequest['group'] : 'banner_id';

        $bannerStat = new VkBannerStat();

        $data = $bannerStat->getData($filter , $sort, $limit , $offset , $group );

        $data = $this->prepareData($data);

        return response()->json(
            [
                'count' => count($data),
                'data' => $data
            ]
        );

    }

    private function prepareData(bool|array|VkBannerStat $data)
    {
        if (empty($data)) {
            return [];
        }
        if (is_array($data)) {
            foreach ($data as  &$value) {
                $value['banner_name'] = $value['city'];
                if($value['section']) {
                    $value['banner_name'] .= ' | ';
                    $value['banner_name'] .= $value['section'];
                }
                if($value['subsection']){
                    $value['banner_name'] .= ' | ';
                    $value['banner_name'] .= $value['subsection'];
                }
                $value['banner_name'] .= ' | ';
                $value['banner_name'] .= $value['name'];
                unset($value['city']);
                unset($value['section']);
                unset($value['subsection']);
                unset($value['name']);
            }
        }
        return $data;
    }
}
