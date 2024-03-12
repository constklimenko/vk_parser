<?php

namespace App\Models;

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
     * Получает запись из таблицы vk_banners
     * @param $banner_id
     * @return VkBannerStat|false
     */
    public static function get($banner_id, $date): array|false
    {
        $data = self::where('banner_id', $banner_id)->where('date', $date)->first();
        return $data->toArray();
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
