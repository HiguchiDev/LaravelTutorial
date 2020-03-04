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



        // 取得したいwebサイトを読み込み
        //$html = file_get_contents($shopInfo['url']);
        //echo phpQuery::newDocument($html)->find("img")->text("src");

        //var_dump($html);
        //echo phpQuery::newDocument($html)->find("h3")->text();

        return view('shopSearch/index', compact('shopInfo', 'latitudeAndLongitude'));
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
