<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VkBannerStat extends Model
{
    use HasFactory;

    public int $banner_id;
    public int $shows;
    public int $clicks;
    public int $leads;
    public string $date;
    public float $spent;
    public $timestamps = false;


    /**
     *
     *  Получает запись из таблицы vk_banners и vk_banner_stats
     *
     *
     * @param array $filter - фильтр, массив с параметрами:
     * date_start - дата начала периода за который суммируется статистика
     * date_end - дата окончания
     * city - город
     * например: ['city' => 'Москва', 'date_end' => '2024-03-12']
     *
     * @param array $sort
     * @param int $limit
     * @param int $offset
     * @param string $group
     * @return VkBannerStat|false
     * /
     * @return array|false
     */
    public function get(array $filter = [], array $sort = [], int $limit = 10, int $offset = 0, string $group = 'banner_id'): array|false
    {
        $vkBannerStat = $this;

        //фильтрация
        if(!empty($filter)){
            foreach ($filter as $key => $value) {
                if(stripos($key, 'date_') !== false){
                    if($key == 'date_start'){
                        $vkBannerStat = $vkBannerStat->where('date', '>', $value);
                    }
                    if($key == 'date_end'){
                        $vkBannerStat = $vkBannerStat->where('date', '<=', $value);
                    }
                }else{
                    $vkBannerStat = $vkBannerStat->where($key, $value);
                }
            }
        }else{
            $vkBannerStat = $vkBannerStat->where('date', '<', date('Y-m-d'));
        }

        //группировка
        $querySelect = [];
        $querySelect[] = 'vk_banners.city';
        $querySelect[] = 'vk_banners.section';
        $querySelect[] = 'vk_banners.subsection';
        $querySelect[] = 'vk_banners.name';
        if(!empty($group)){
            $querySelect[] = 'vk_banner_stats.'.$group;
            $querySelect[] = 'sum(shows) as shows';
            $querySelect[] = 'sum(clicks) as clicks';
            $querySelect[] = 'sum(leads) as leads';
            $querySelect[] = 'sum(spent) as spent';//потрачено на показ
            $querySelect[] = 'IFNULL(sum(spent)/ NULLIF(sum(leads),0),0) as cpl'; //средняя стоимость лида
            $querySelect[] = 'IFNULL((sum(clicks)* 100)/ NULLIF(sum(shows),0),0) as ctr';//конверсия показов в клики
            $querySelect = implode(',', $querySelect);
            $vkBannerStat->select(DB::raw($querySelect));
            $vkBannerStat = $vkBannerStat->groupBy('vk_banner_stats.'.$group);
        }else{
            $querySelect[] = 'vk_banner_stats.banner_id';
            $querySelect[] = 'shows';
            $querySelect[] = 'clicks';
            $querySelect[] = 'leads';
            $querySelect[] = 'spent';//потрачено на показ
            $querySelect[] = 'IFNULL(spent/ NULLIF(leads,0),0) as cpl';//средняя стоимость лида
            $querySelect[] = 'IFNULL((clicks * 100)/ NULLIF(shows,0),0) as ctr';//конверсия показов в клики
            $querySelect = implode(',', $querySelect);
            $vkBannerStat->select(DB::raw($querySelect));
        }

        //сортировка
        if(!empty($sort)){
            foreach ($sort as $key => $value) {
                $vkBannerStat = $vkBannerStat->orderBy($key, $value);
            }
        }

        //соединение
        $vkBannerStat->leftJoin('vk_banners', 'vk_banners.banner_id', '=', 'vk_banner_stats.banner_id');

        //пагинация
        $vkBannerStat->limit($limit);
        $vkBannerStat->offset($offset);

        try {
            $data = $vkBannerStat->get();
            return $data->toArray();
        }catch (Exception $e){
            Log::error($e->getMessage());
            return false;
        }
    }

    public static function getLastBannerDate($id)
    {
        $result = self::where('banner_id',$id)->orderByDesc('date')->first();
        if(!empty($result) && !empty($result->toArray()['date'])){
            return $result->toArray()['date'];
        }
        return false;
    }

    /**
     * Добавляет новую запись в таблицу
     * @return bool
     */
    public function add($banner_id = 0, $shows = 0, $clicks = 0, $leads = 0, $date = '', $spent = 0): bool
    {
        return self::insert([
            'banner_id' => $banner_id,
            'shows' => $shows,
            'clicks' => $clicks,
            'leads' => $leads,
            'date' => $date,
            'spent' => $spent
        ]);
    }

    /**
     * Возвращает общее количество уникальных баннеров
     * @return int
     */
    public static function getBannersNumber(): int
    {
        return self::select(DB::raw('count(DISTINCT banner_id) as number'))->get()[0]->number;
    }

    public static function getLastBannerId(): int
    {
        return self::select(DB::raw('max(banner_id) as banner_id'))->get()[0]->banner_id;
    }

    public static function getLastDateById($id): string|false
    {
        $r = self::select(DB::raw('max(date) as date'))->where('banner_id', $id)->get();
        if( $r->count() > 0 ) {
            return $r[0]->date;
        }
        return false;
    }

}
