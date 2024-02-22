<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class VkBanner extends Model
{
    use HasFactory;

    public int $banner_id;
    public int $campaign_id;
    public int $group_id;
    public string $name;
    public string $city;
    public string $section;
    public string $subsection;
    public $timestamps = false;
    protected $primaryKey = 'banner_id';

    public function __construct(int $banner_id, int $campaign_id, int $group_id, string $name, string $city, string $section, string $subsection)
    {
        $this->banner_id = $banner_id;
        $this->campaign_id = $campaign_id;
        $this->group_id = $group_id;
        $this->name = $name;
        $this->city = $city;
        $this->section = $section;
        $this->subsection = $subsection;

        parent::__construct();
    }

    public function add(): bool
    {
        return self::insert([
            'banner_id' => $this->banner_id,
            'campaign_id' => $this->campaign_id,
            'group_id' => $this->group_id,
            'name' => $this->name,
            'city' => $this->city,
            'section' => $this->section,
            'subsection' => $this->subsection,
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
