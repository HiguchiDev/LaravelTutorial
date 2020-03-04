<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
                
        $randomIndex = rand(0, 99);
        $shopInfoList = array_slice($jsonList, $randomIndex, 1, true);   //なんでキーがrandomIndexの連想配列が返却されるんだ・・・？
        $shopInfo = $shopInfoList[$randomIndex];
        
        $latitudeAndLongitude = "緯度：" . $request->session()->get($sessionId . 'latitude') . " ";
        $latitudeAndLongitude .= "経度：" . $request->session()->get($sessionId . 'longitude');

        $distance = $this->location_distance(
            $shopInfo["latitude"],
            $shopInfo["longitude"],
            $request->session()->get($sessionId . 'latitude'),
            $request->session()->get($sessionId . 'longitude')
        );

        $distance["distance"];
        // 取得したいwebサイトを読み込み
        //$html = file_get_contents($shopInfo['url']);
        //echo phpQuery::newDocument($html)->find("img")->text("src");

        //var_dump($html);
        //echo phpQuery::newDocument($html)->find("h3")->text();

        return view('shopSearch/index', compact('shopInfo', 'distance'));
    }
    //GPSなどの緯度経度の２点間の直線距離を求める（世界測地系）

    //$lat1, $lon1 --- A地点の緯度経度
    //$lat2, $lon2 --- B地点の緯度経度
    public function location_distance($lat1, $lon1, $lat2, $lon2)
    {
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
    }

    private function getPosition($address)
    {
        mb_language("Japanese");//文字コードの設定
        mb_internal_encoding("UTF-8");

        //住所を入れて緯度経度を求める。
        $myKey = "AIzaSyDkAkXUpnqoNqbhQ8sdzM7URod4sZYxUr0";

        $address = urlencode($address);

        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $address . "+CA&key=" . $myKey ;

        $contents= file_get_contents($url);
        $jsonData = json_decode($contents, true);

        $lat = $jsonData["results"][0]["geometry"]["location"]["lat"];
        $lng = $jsonData["results"][0]["geometry"]["location"]["lng"];
        
        return array($lat, $lng);
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
