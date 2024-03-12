<?php

namespace App\Models;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class VkAds
{
    public string $ads_url;
    public Client $client;
    public string $access_token;

    public function __construct(){
        $this->client = new Client();
        $this->ads_url = env('VK_ADS_URL');
        $this->access_token =(new VkAdsTokens())->getAccessToken();
    }
    public function get(string $path, $params = []){
        return $this->tryGet($path, $params);
    }

    private function tryGet(string $path, $params = []){
        try {
            $response = $this->client->request('GET', $this->ads_url . $path, $params);
            sleep(0.6);
        } catch (GuzzleException $e) {
            $exceptionArr = json_decode($e->getResponse()->getBody()->getContents())->toArray();
            Log::debug($exceptionArr);
            if($exceptionArr['remaining']['1'] == 0){
                sleep(0.6);
            }
            if($exceptionArr['remaining']['3600'] == 0){
                sleep(300);
            }
            return $this->tryGet($path, $params);
        }
        return json_decode($response->getBody()->getContents(), true);
    }

    public function getLeads( int $limit = 1,int $offset = 0, string $sorting = 'id' ): array
    {
        $params = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->access_token,
            ],
            'query' => [
                'limit' => $limit,
                'offset' => $offset,
                'sorting' => $sorting
            ]
        ];
        $path = 'v1/lead_ads/leads.json';

        return $this->get($path, $params);
    }

    public function getBannersList( int $limit = 200 ): array
    {
        $activeBanners = $this->getBannersListOneStatus('active', $limit);
        $blockedBanners = $this->getBannersListOneStatus('blocked', $limit);
        $deletedBanners = $this->getBannersListOneStatus('deleted', $limit);
        return array_merge($activeBanners, $blockedBanners, $deletedBanners);
    }

    public function getBannersListOneStatus( string $status = 'active', int $limit = 200): array
    {
        $bannersList = [];
        $firstBanner = $this->getBannerListIteration($status);
        if(!empty($firstBanner['count'])){
            $apiBannersNumber =  $firstBanner['count'];
            $iterations = ceil($apiBannersNumber / $limit);

            for ($i = 0; $i < $iterations; $i++) {
                $banners = $this->getBannerListIteration($status, $limit, $i * $limit, '-id');
                foreach ($banners['items'] as $banner) {
                    $bannersList[$banner['id']] = $banner;
                    $bannersList[$banner['id']]['status'] = $status;
                }
            }
            return $bannersList;
        }

    }

    public function getBannerListIteration(string $status, int $limit = 1,int $offset = 0, string $sorting = 'id' ): array
    {
        sleep(1);
        $params = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->access_token,
            ],
            'query' => [
                'limit' => $limit,
                'offset' => $offset,
                'sorting' => $sorting,
                '_status' => $status
            ]
        ];
        $path = 'v2/banners.json';
        return $this->get($path, $params);
    }

    /**
     * Возвращает статистику по баннеру
     * Колонки в ответе:
     * shows - количество показов;
     * clicks - количество кликов;
     * goals - количество достижений целей (цели Top@Mail.ru для сайтов и установок для мобильных приложений);
     * spent - списания;
     * cpm - среднее списание за 1000 просмотров;
     * cpc - среднее списание за 1 клик;
     * cpa - среднее списание за достижение 1 цели;
     * ctr - процентное отношение количества кликов к количеству просмотров;
     * cr - процентное отношение количества достижений целей к количеству кликов.
     * @param int $banner_id
     * @param string $dateString
     * @return array
     */
    public function getBannerStatistics(int $banner_id, string $dateString = '' ): array
    {
        // Получаем текущую дату
        $currentDate = Carbon::now();

        // Вычитаем один год и один день из текущей даты
        $allowedDate = $currentDate->subYear();

        if( $dateString == '' )
        {
            $dateString = $allowedDate->format('Y-m-d');
        }else{
            $dateFromString = Carbon::parse($dateString);
            if( $dateFromString < $allowedDate ){
                $dateString = $allowedDate->format('Y-m-d');
            }
        }

        $params = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->access_token,
            ],
            'query' => [
                'id' => $banner_id,
                'date_from'=> $dateString
            ]
        ];
        $path = 'v2/statistics/banners/day.json';
        $result = $this->get($path, $params);
        if (!empty($result['items'])) {
            return $result['items'][0]['rows'];
        }
        sleep(0.5);
        return [];
    }

    /**
     * Загружает группу объявлений
     * @param int $group_id
     * @return array|mixed
     */
    public function getGroup(int $group_id)
    {
        $params = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->access_token,
            ],
        ];
        $path = 'v2/ad_groups/'. $group_id .'.json';
        return $this->get($path, $params);
    }

    /**
     * Загружает лиды определённого объявления за определённую дату
     * @param $banner_id
     * @param string $date
     * @param $limit
     * @param $offset
     * @return array|mixed
     */
    public function getLeadsByBannerAndDate($banner_id, string $date, $limit = 10, $offset = 0)
    {
        $firstDate = Carbon::parse( $date)->format('Y-m-d').'%2000:00:00';
        $secondDate = Carbon::parse( $date)->addDays(1)->format('Y-m-d').'%2000:00:00';
        $params = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->access_token,
            ],
            'query' => [
                '_banner_ids__in' => $banner_id,
                '_created_at__gte' =>urldecode($firstDate),
                '_created_at__lte' => urldecode($secondDate),
                'limit' => $limit,
                'offset' => $offset
            ]
        ];
        $path = 'v1/lead_ads/leads.json';
        return $this->get($path, $params);
    }

    /**
     * Загружает весь список лидов объявления
     * @param $banner_id
     * @return array
     */
    public function getLeadsListByBanner($banner_id)
    {
        $offset = 0;
        $limit = 100;
        $items = [];
        do{
            $result =  $this->getLeadsListByBannerIteration($banner_id, $offset);
            if(!empty($result['items'])){
                $items = array_merge($items, $result['items']);
                $offset += $limit;
            }
        }while($result['count'] > $offset);

        if (!empty($items)) {
            $leadsList  = [];
            foreach ($items as $item) {
                $date = Carbon::parse($item['created_at'])->format('Y-m-d');
                if (!isset($leadsList[$date])) {
                    $leadsList[$date] = 0;
                }
                $leadsList[$date] += 1;
            }
            return $leadsList;
        }
        return [];
    }

    private function getLeadsListByBannerIteration($banner_id, $offset = 0, )
    {
        $params = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->access_token,
            ],
            'query' => [
                '_banner_ids__in' => $banner_id,
                'limit' => 100,
                'offset' => $offset
            ]
        ];
        $path = 'v1/lead_ads/leads.json';
        return  $this->get($path, $params);
    }

}
