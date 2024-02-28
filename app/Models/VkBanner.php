<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class VkBanner extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'banner_id';


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

    public static function getBannerArrayById(int $banner_id): mixed
    {
        Log::info( 'addBannerRowId: '.$banner_id);
        $result = self::where('banner_id', $banner_id)->first();
        Log::info(' getBannerArrayById result: ', $result);
        if (!empty($result->banner_id)) {
            return $result;
        } else {
            return false;
        }
    }
}
