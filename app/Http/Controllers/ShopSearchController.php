<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShopSearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // セッションの値を全て取得
        $data = $request->session()->all();

        //var_dump($data);

        //https://api.gnavi.co.jp/RestSearchAPI/v3/?keyid=(発行されたアクセスキー）&range=1&sort=2
        $reqURL = 'https://api.gnavi.co.jp/RestSearchAPI/v3/?keyid=2e49b5d121c0f40773b048f7b7d403f8&range=5&input_coordinates_mode=2&hit_per_page=100';
        $reqURL .= '&latitude=' . $request->session()->get('latitude');
        $reqURL .= '&longitude=' . $request->session()->get('longitude');
        

        //file_get_contentsでレスポンスを処理
        $json = file_get_contents($reqURL);
        //JSONをデコード
        $json_values = json_decode($json, true);

        $latitudeAndLongitude = "緯度：" . $request->session()->get('latitude') . " ";
        $latitudeAndLongitude .= "経度：" . $request->session()->get('longitude');

        $randomIndex = rand(0, 99);
        $shopInfoJson = array_slice($json_values['rest'], $randomIndex, 1, true);   //なんでキーがrandomIndexの連想配列が返却されるんだ・・・？
        $shopInfo = $shopInfoJson[$randomIndex];
        
        return view('shopSearch/index', compact('shopInfo', 'latitudeAndLongitude'));
    }

    /**
     * Show the form for creating a new resource.
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
        $request->session()->put('latitude', $request->latitude);
        $request->session()->put('longitude', $request->longitude);

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
