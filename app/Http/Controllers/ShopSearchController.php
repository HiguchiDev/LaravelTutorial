<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use AppReference\ApiKeys;
//require_once 'ApiKeys.php';

// phpQueryの読み込み
//require_once("phpQuery-onefile.php");
        
class ShopSearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    
        $sessionId = $request->session()->getId();
        
        if (is_null($request->session()->get($sessionId . 'latitude')) || is_null($request->session()->get($sessionId . 'longitude'))) {
            return view('shopSearch/session_faild');
        }

        $shopInfoSessionKey = $sessionId . "ShopSessionKey";
        $jsonList;

        if ($request->session()->has($shopInfoSessionKey)) {
            //echo "セッションから取得";
            $jsonList = $request->session()->get($shopInfoSessionKey, array());
        } else {
            //echo "APIから取得";
            $jsonList = $this->getShopInfoFromAPI($request);
            $request->session()->put($shopInfoSessionKey, $jsonList);
        }
        
        $max = 99;

        if(count($jsonList) <= $max){
            $max = count($jsonList) - 1;
        }

        $randomIndex = rand(0, $max);
        $shopInfoList = array_slice($jsonList, $randomIndex, 1, true);   //なんでキーがrandomIndexの連想配列が返却されるんだ・・・？
        $shopInfo = $shopInfoList[$randomIndex];
        
        $latitudeAndLongitude = "緯度：" . $request->session()->get($sessionId . 'latitude') . " ";
        $latitudeAndLongitude .= "経度：" . $request->session()->get($sessionId . 'longitude');

        $distance = $this->location_distance(
            $this->getAddress($request->session()->get($sessionId . 'latitude'), $request->session()->get($sessionId . 'longitude')),
            $shopInfo["address"]
        );

        

        if (empty($shopInfo['image_url']['shop_image1'])) {
            
            $shopImage = $this->getShopImageFromGoogleImageSearch($shopInfo['name']);

            $shopInfo['image_url']['shop_image1'] = $shopImage;
            
            //echo "aa";
        }
        else{
            //echo "bb";
        }

        return view('shopSearch/index', compact('shopInfo', 'distance'));
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
    public function location_distance($from, $to)
    {
        
        $from = urlencode($from);
        $to = urlencode($to);

        
        //echo $to;
        $reqURL = 'https://maps.googleapis.com/maps/api/directions/json?origin=';
        $reqURL .= $from;
        $reqURL .= '&destination=';
        $reqURL .= $to;
        $reqURL .= '&mode=walking&language=ja&key=' . 'AIzaSyDkAkXUpnqoNqbhQ8sdzM7URod4sZYxUr0';

        //echo ApiKeys::getGoogleApiKey();
        //var_dump( $reqURL);
        
        //file_get_contentsでレスポンスを処理
        $json = file_get_contents($reqURL);
        //JSONをデコード
        $jsonList = json_decode($json, true);


        return $jsonList['routes'][0]['legs'][0]['distance'];

        /*
        $lat_average = deg2rad($lat1 + (($lat2 - $lat1) / 2));//２点の緯度の平均
        $lat_difference = deg2rad($lat1 - $lat2);//２点の緯度差
        $lon_difference = deg2rad($lon1 - $lon2);//２点の経度差
        $curvature_radius_tmp = 1 - 0.00669438 * pow(sin($lat_average), 2);
        $meridian_curvature_radius = 6335439.327 / sqrt(pow($curvature_radius_tmp, 3));//子午線曲率半径
        $prime_vertical_circle_curvature_radius = 6378137 / sqrt($curvature_radius_tmp);//卯酉線曲率半径
        
    //２点間の距離
        $distance = pow($meridian_curvature_radius * $lat_difference, 2) + pow($prime_vertical_circle_curvature_radius * cos($lat_average) * $lon_difference, 2);
        $distance = sqrt($distance);
    
        $distance_unit = round($distance);
        if ($distance_unit < 1000) {//1000m以下ならメートル表記
            $distance_unit = $distance_unit."m";
        } else {//1000m以上ならkm表記
            $distance_unit = round($distance_unit / 100);
            $distance_unit = ($distance_unit / 10)."km";
        }
    
        //$hoge['distance']で小数点付きの直線距離を返す（メートル）
        //$hoge['distance_unit']で整形された直線距離を返す（1000m以下ならメートルで記述 例：836m ｜ 1000m以下は小数点第一位以上の数をkmで記述 例：2.8km）
        return array("distance" => $distance, "distance_unit" => $distance_unit);
        */
    }

    private function getAddress($latitude, $longitude)
    {
        mb_language("Japanese");//文字コードの設定
        mb_internal_encoding("UTF-8");

        //住所を入れて緯度経度を求める。
        //$address = urlencode($address);

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
