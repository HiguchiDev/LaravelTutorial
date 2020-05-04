<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use AppReference\ApiKeys;
use App\Jobs\CalcDistance;
use App\ShopImage;

//require_once 'ApiKeys.php';

// phpQueryの読み込み
//require_once("phpQuery-onefile.php");
        
class ShopSearchController extends Controller
{
    public function getDistance(Request $request)
    {

        /*
        $sessionId = $request->session()->getId();
        $latitude = $request->session()->get($sessionId . 'latitude');
        $longitude = $request->session()->get($sessionId . 'longitude');

        //緯度経度が取得できなければセッションエラー
        if (is_null($latitude) || is_null($longitude)) {
            return view('shopSearch/session_faild');
        }

        */


        $sessionId = $request->session()->getId();
        $latitude = $request->session()->get($sessionId . 'latitude');
        $longitude = $request->session()->get($sessionId . 'longitude');
        $shopInfo = $request->session()->get($sessionId . 'shopInfo');

        if (is_null($latitude) || is_null($longitude) || is_null($shopInfo)) {
            return '距離取得エラー';
        }

        $distance = $this->getLocationDistance(
            $this->getAddress($latitude, $longitude),
            $shopInfo["address"]
        );

        return $distance['text'];
        /*
        echo $request->input('longitude');

        $value_array = ['value1'=>1];
        $json =  json_encode($value_array);
        return response()->json($json);*/
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sessionId = $request->session()->getId();
        $latitude = $request->session()->get($sessionId . 'latitude');
        $longitude = $request->session()->get($sessionId . 'longitude');
        
        //緯度経度が取得できなければセッションエラー
        if (is_null($latitude) || is_null($longitude)) {
            return view('shopSearch/session_faild');
        }

        $shopJsonList = $this->getShopJsonList($request);

        $shopInfo = $this->getRandomShopInfo($shopJsonList);
        $request->session()->put($sessionId . 'shopInfo', $shopInfo);
        if (empty($shopInfo['image_url']['shop_image1'])) {
            
            $shopImage = $this->getShopImageFromGoogleImageSearch($shopInfo['name']);

            $shopInfo['image_url']['shop_image1'] = $shopImage;
        }
        else{
        }

        ShopImage::insertShopImage($shopInfo['name'], $shopInfo["address"], $shopInfo['image_url']['shop_image1']);

        //phpinfo();
        //CalcDistance::dispatch()->delay(now()->addMinutes(1));

        return view('shopSearch/index', compact('shopInfo'));
    }

    public function getShopJsonList($request){
        $sessionId = $request->session()->getId();
        $shopInfoSessionKey = $sessionId . "ShopSessionKey";
        $shopJsonList;

        if ($request->session()->has($shopInfoSessionKey)) {
            //echo "セッションから取得";
            $shopJsonList = $request->session()->get($shopInfoSessionKey, array());
            \Log::info('セッションからショップ情報取得');

        } else {
            //echo "APIから取得";
            $shopJsonList = $this->getShopInfoFromAPI($request);
            $request->session()->put($shopInfoSessionKey, $shopJsonList);
            \Log::info('Google APIからショップ情報取得');
        }

        return $shopJsonList;
    }

    public function getRandomShopInfo($shopJsonList){
        $max = 99;

        if(count($shopJsonList) <= $max){
            $max = count($shopJsonList) - 1;
        }

        $randomIndex = rand(0, $max);
        $shopInfoList = array_slice($shopJsonList, $randomIndex, 1, true);   //なんでキーがrandomIndexの連想配列が返却されるんだ・・・？
        return $shopInfoList[$randomIndex];
    }

    public function getShopImageFromGoogleImageSearch($word){

        
        $baseurl = 'https://www.googleapis.com/customsearch/v1?key=AIzaSyDEOrfJPaO-lKcER3wCW8blUoRo99lYdDU&cx=009073077464763218888:4rjpzcdd1zq&searchType=image&q=';
        //$word = urlencode($word);
        //$baseurl .= $word;
        //echo "URL" . $baseurl;
        //$baseurl = 'https://www.googleapis.com/customsearch/v1?key=AIzaSyDEOrfJPaO-lKcER3wCW8blUoRo99lYdDU&cx=009073077464763218888:4rjpzcdd1zq&searchType=image&q=%E4%B8%8A%E9%87%8E%E6%96%87%E4%B9%9F';
        
        $myurl=$baseurl.urlencode($word);
        $myjson=file_get_contents($myurl);
        $recs=json_decode($myjson,true);
        
        //var_dump($recs['items']);

        if(empty($recs['items'])){
            return '';
        }

        return $recs['items'][0]['link'];   //一番最初に出た結果を格納
                
        //return $str;

    }

    //GPSなどの緯度経度の２点間の直線距離を求める（世界測地系）

    //$lat1, $lon1 --- A地点の緯度経度
    //$lat2, $lon2 --- B地点の緯度経度
    public function getLocationDistance($from, $to)
    {
        
        $from = urlencode($from);
        $to = urlencode($to);

        $reqURL = 'https://maps.googleapis.com/maps/api/directions/json?origin=';
        $reqURL .= $from;
        $reqURL .= '&destination=';
        $reqURL .= $to;
        $reqURL .= '&mode=walking&language=ja&key=' . 'AIzaSyDkAkXUpnqoNqbhQ8sdzM7URod4sZYxUr0';

        //file_get_contentsでレスポンスを処理
        $json = file_get_contents($reqURL);
        //JSONをデコード
        $jsonList = json_decode($json, true);


        return $jsonList['routes'][0]['legs'][0]['distance'];
    }

    private function getAddress($latitude, $longitude)
    {
        mb_language("Japanese");//文字コードの設定
        mb_internal_encoding("UTF-8");

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $latitude . ',' . $longitude . '&key=AIzaSyDkAkXUpnqoNqbhQ8sdzM7URod4sZYxUr0&language=ja';

        $contents= file_get_contents($url);
        $jsonData = json_decode($contents, true);

        return str_replace('日本、', '', $jsonData["results"][0]["formatted_address"]);
    }

    private function getShopInfoFromAPI(Request $request)
    {
        $sessionId = $request->session()->getId();
        $reqURL = 'https://api.gnavi.co.jp/RestSearchAPI/v3/?keyid=2e49b5d121c0f40773b048f7b7d403f8&range=5&input_coordinates_mode=2&hit_per_page=100';
        $reqURL .= '&latitude=' . $request->session()->get($sessionId . 'latitude');
        $reqURL .= '&longitude=' . $request->session()->get($sessionId . 'longitude');

        //file_get_contentsでレスポンスを処理
        $json = file_get_contents($reqURL);
        //JSONをデコード
        $jsonList = json_decode($json, true);

        return $jsonList['rest'];
    }

    /**
     * Show the form for creating a new resource.///////////////////////////////////////////////////-　　　
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

        $request->session()->regenerate();
        $sessionId = $request->session()->getId();

        $request->session()->put($sessionId . 'latitude', $request->latitude);
        $request->session()->put($sessionId . 'longitude', $request->longitude);

        return redirect("/shopSearch");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
