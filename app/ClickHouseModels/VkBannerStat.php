<?php

namespace App\ClickHouseModels;

use App\ClickHouseModels\ClickHouseClient as Client;
use Illuminate\Support\Facades\Log;
use App\ClickHouseModels\Model;

class VkBannerStat extends Model
{
    public function __construct()
    {
        $this->table = 'vk_banner_stats';
        parent::__construct();
    }

    public function getData(array $filter = [], array $sort = [], int $limit = 10, int $offset = 0, string $group = '')
    {
        $client = new Client();
        $query = "SELECT ";
        $queryFrom = " FROM vk_banner_stats AS bs JOIN vk_banners AS b ON bs.banner_id = b.id ";
        //фильтрация
        if(!empty($filter)){
            $queryWhere = "WHERE ";
            $queryWhereArr = [];
            foreach ($filter as $key => $value) {
                if(stripos($key, 'date_') !== false){
                    if($key == 'date_start'){
                        $queryWhereArr[] = "bs.date > '{$value}'";
                    }
                    if($key == 'date_end'){
                        $queryWhereArr[] = "bs.date <= '{$value}'";
                    }
                }else{
                    $queryWhereArr[] = "{$key} = '{$value}'";
                }
            }
            $queryWhere .= implode(' AND ', $queryWhereArr);
        }else{
            $curDate=  date('Y-m-d');
            $queryWhere = " WHERE bs.date < '{$curDate}'";
        }

        //группировка
        $querySelectArr = [];
        $querySelectArr[] = 'bs.banner_id as banner_id';
        $querySelectArr[] = 'b.city as city';
        $querySelectArr[] = 'b.`section` as section';
        $querySelectArr[] = 'b.subsection   as subsection';
        $querySelectArr[] = 'b.name';

        if(!empty($group)){
            $querySelectArr[] = 'sum(bs.shows) as shows';
            $querySelectArr[] = 'sum(bs.clicks) as clicks';
            $querySelectArr[] = 'sum(bs.leads) as leads';
            $querySelectArr[] = 'sum(bs.spent) as spent';//потрачено на показ
            $querySelectArr[] = 'IFNULL(sum(bs.spent)/ NULLIF(sum(bs.leads),0),0) as cpl'; //средняя стоимость лида
            $querySelectArr[] = 'IFNULL((sum(bs.clicks)* 100)/ NULLIF(sum(bs.shows),0),0) as ctr';//конверсия показов в клики

            $queryGroup = " GROUP BY ";
            $queryGroupArr = [];
            $queryGroupArr[] = 'bs.banner_id';
            $queryGroupArr[] = 'b.city';
            $queryGroupArr[] = 'b.`section` ';
            $queryGroupArr[] = 'b.subsection ';
            $queryGroupArr[] = 'b.name';
            $queryGroupArr[] = 'b.id';
            if( !in_array('b.'.$group, $queryGroupArr)){
                $queryGroupArr[]= 'bs.'.$group;
            }
            $queryGroup .= implode(', ', $queryGroupArr);
        }else{
            $querySelectArr[] = 'bs.shows as shows';
            $querySelectArr[] = 'bs.clicks as clicks';
            $querySelectArr[] = 'bs.leads as leads';
            $querySelectArr[] = 'bs.spent as spent';//потрачено на показ
            $querySelectArr[] = 'IFNULL(spent/ NULLIF(leads,0),0) as cpl';//средняя стоимость лида
            $querySelectArr[] = 'IFNULL((clicks * 100)/ NULLIF(shows,0),0) as ctr';//конверсия показов в клики
        }

        //сортировка
        if(!empty($sort)){
            $queryOrder = " ORDER BY ";
            $queryOrderArr = [];
            foreach ($sort as $key => $value) {
                $queryOrderArr[] = "bs.{$key} {$value}";
            }
            $queryOrder .= implode(', ', $queryOrderArr);
        }else{
            $queryOrder = "ORDER BY bs.banner_id DESC";
        }

        //лимит и офсет
        $queryLimit = " LIMIT {$limit}";
        $queryOffset = " OFFSET {$offset}";

        $query .= implode(', ', $querySelectArr);
        $query .= $queryFrom;
        $query .= $queryWhere;
        $query .= $queryGroup;
        $query .= $queryOrder;
        $query .= $queryLimit;
        $query .= $queryOffset;

        Log::info($query);

        return $client->select($query)->rows();
    }

    public static function getLastBannerDate($id)
    {
        $client = new Client();
        $table = (new VkBannerStat)->getTableName();
        $query = "SELECT max(date) as date FROM {$table} WHERE banner_id = {$id}";
        $result = $client->select($query)->rows();
        return $result[0]['date'];
    }

    public function add($banner_id = 0, $shows = 0, $clicks = 0, $leads = 0, $date = '', $spent = 0)
    {
        $client = new Client();
        $table = $this->getTableName();
        $query = "INSERT INTO {$table} (banner_id, shows, clicks, leads, spent, date) VALUES ({$banner_id}, {$shows}, {$clicks}, {$leads}, {$spent}, '{$date}')";
        $res = $client->write($query);
        return !$res->isError();
    }

}
