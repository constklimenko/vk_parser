<?php

namespace App\ClickHouseModels;

use App\ClickHouseModels\ClickHouseClient as Client;

class VkBannerStat
{
    public function getData(array $filter = [], array $sort = [], int $limit = 10, int $offset = 0, string $group = 'banner_id')
    {
        $client = new Client();
        $query = "SELECT";
        $queryFrom = "FROM vk_banner_stats AS bs JOIN vk_banners AS b ON bs.banner_id = b.banner_id";
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
                    $queryWhereArr[] = "b.{$key} = '{$value}'";
                }
            }
            $queryWhere .= implode(' AND ', $queryWhereArr);
        }else{
            $curDate=  date('Y-m-d');
            $queryWhere = "WHERE b.date < '{$curDate}'";
        }

        //группировка
        $querySelectArr = [];
        $querySelectArr[] = 'bs.banner_id';
        $querySelectArr[] = 'b.city';
        $querySelectArr[] = 'b.section';
        $querySelectArr[] = 'b.subsection';
        $querySelectArr[] = 'b.name';

        if(!empty($group)){
            $querySelectArr[] = 'sum(bs.shows) as shows';
            $querySelectArr[] = 'sum(bs.clicks) as clicks';
            $querySelectArr[] = 'sum(bs.leads) as leads';
            $querySelectArr[] = 'sum(bs.spent) as spent';//потрачено на показ
            $querySelectArr[] = 'IFNULL(sum(bs.spent)/ NULLIF(sum(bs.leads),0),0) as cpl'; //средняя стоимость лида
            $querySelectArr[] = 'IFNULL((sum(bs.clicks)* 100)/ NULLIF(sum(bs.shows),0),0) as ctr';//конверсия показов в клики

            $queryGroup = "GROUP BY ";
            $queryGroupArr = [];
            $queryGroupArr[] = 'bs.banner_id as banner_id';
            $queryGroupArr[] = 'b.city as city';
            $queryGroupArr[] = 'b.section as section';
            $queryGroupArr[] = 'b.subsection as subsection';
            $queryGroupArr[] = 'b.name as name';
            if( !in_array('b.'.$group, $queryGroupArr)){
                $queryGroupArr[]= 'b.'.$group;
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
            $queryOrder = "ORDER BY ";
            $queryOrderArr = [];
            foreach ($sort as $key => $value) {
                $queryOrderArr[] = "b.{$key} {$value}";
            }
            $queryOrder .= implode(', ', $queryOrderArr);
        }else{
            $queryOrder = "ORDER BY bs.banner_id DESC";
        }

        //лимит и офсет
        $queryLimit = "LIMIT {$limit}";
        $queryOffset = "OFFSET {$offset}";

        $query .= implode(', ', $querySelectArr);
        $query .= $queryFrom;
        $query .= $queryWhere;
        $query .= $queryGroup;
        $query .= $queryOrder;
        $query .= $queryLimit;
        $query .= $queryOffset;

        return $client->select($query)->rows();
    }

}
