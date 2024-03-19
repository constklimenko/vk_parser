<?php

namespace App\Service;

use App\Models\SuccessParsingMessenger;
use App\Models\VkAds;
use App\ClickHouseModels\VkBanner as ClickHouseVkBanner;
use App\ClickHouseModels\VkBannerStat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VkAdsParser
{
    private VkAds $ads;
    private int $limit = 200;

    public function __construct(){
        $this->ads = new VkAds();
    }

    public function parse()
    {
        $messenger = new SuccessParsingMessenger();
        $date_start = date( 'Y-m-d H:i:s' );
        try{
            $bannersList = $this->ads->getBannersList();
            foreach ($bannersList as $banner) {
                $this->updateDB($banner);
            }

        }catch (\Exception $e){
            $date_end = date( 'Y-m-d H:i:s' );
            $messenger->sendMessage('скрипт, начавший работу в '. $date_start.' завершил работу с ошибкой в ' . $date_end .'; Ошибка - '. $e->getMessage());
            return;
        }
        $date_end = date( 'Y-m-d H:i:s' );
        $messenger->sendMessage('скрипт, начавший работу в '. $date_start.' завершил работу без ошибок в ' . $date_end);
    }

    private function updateDB($banner)
    {
        $id = $banner['id'];
        $banner = $this->addNameFromGroup($banner);
        $this->addRowToVkBanners($banner);
        $lastExistingDate = VkBannerStat::getLastBannerDate($id);
        //пропускаем старые данные
        if(Carbon::now()->format('Y-m-d') <= $lastExistingDate){
            return true;
        }

        if($lastExistingDate){
            $bannerArray = $this->ads->getBannerStatistics($id, $lastExistingDate);
        }else{
            $bannerArray = $this->ads->getBannerStatistics($id);
        }

        if(!empty($bannerArray)){
            $dateList = $this->getDateListById($id);
            $leadsArray = $this->ads->getLeadsListByBanner($id);
            foreach($bannerArray as $item){
                if(!in_array( $item['date'],  $dateList)){
                    $item = $this->prepareItem($item, $leadsArray, $id);
                    $this->addRowToVkBannerStats($item);
                };
            }
        }
        return true;
    }
    private function getDateListById($id){
        $existingDateList =(new  VkBannerStat)->select('date')->where('banner_id', $id)->orderBy('date')->get()->toArray();
        return array_map(function($d){return $d['date'];}, $existingDateList);
    }

    private function addNameFromGroup($banner)
    {
        $group = $this->ads->getGroup($banner['ad_group_id']);
        $nameArray = explode('|', $group['name']);
        $lastWord = $nameArray[count($nameArray) - 1];

        if( stripos($lastWord,'[') !== false){
            $nameArray[count($nameArray) - 2] = $nameArray[count($nameArray) - 2] .' | '.  $lastWord;
            unset($nameArray[count($nameArray) - 1]);
        }

        //распарсить название баннера
        $banner['name'] = trim($nameArray[count($nameArray) - 1]);
        $banner['city'] = trim($nameArray[0]);

        if(count($nameArray) > 2){
            $banner['category'] = trim($nameArray[1]);
        }else{
            $banner['category'] = '';
        }

        if(count($nameArray) > 3){
            $banner['subcategory'] = trim($nameArray[2]);
        }else{
            $banner['subcategory'] = '';
        }

        return $banner;
    }

    private function addRowToVkBannerStats(mixed $item): bool
    {
        $row = new VkBannerStat();
        return $row->add($item['id'], $item['base']['shows'], $item['base']['clicks'], $item['base']['goals'], $item['date'],$item['base']['spent']);
    }

    /**
     * @param mixed $item
     * @param $banner_id
     * @return mixed
     */
    private function prepareItem(array $item, $leadsArray, $banner_id): array
    {
        if(!empty($leadsArray[$item['date']])){
            $item['leads'] = $leadsArray[$item['date']];
        }else{
            $item['leads'] = 0;
        }

        $item['id'] = $banner_id;
        return $item;
    }

    /**
     * @param $banner
     * @return bool
     */
    private function addRowToVkBanners($banner): bool
    {
        $obBanner = (new ClickHouseVkBanner())->select('id')->select('status')->where('id', $banner['id'])->get();

        $model = new ClickHouseVkBanner();
        if (empty($obBanner->toArray())) {
            return $model->insert(
                [
                    'id' => $banner['id'],
                    'campaign_id' => $banner['campaign_id'],
                    'group_id' => $banner['ad_group_id'],
                    'name' => $banner['name'],
                    'city' => $banner['city'],
                    'section' => $banner['category'],
                    'subsection' => $banner['subcategory'],
                    'status' => $banner['status'],
                ]
            );
        }else{
            $arrBanner = $obBanner->toArray();
            if($banner['status'] != $arrBanner[0]['status']) {
                return $model->where('id', $banner['id'])->update(
                    [
                        'status' => $banner['status'],
                    ]
                );
            }
            return true;
        }
    }

}
