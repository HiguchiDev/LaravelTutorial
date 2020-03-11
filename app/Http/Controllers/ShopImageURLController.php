<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShopImageURLController extends Controller
{
    public function index(Request $request)
    {
        $shopImageURL = $this->getShopImageFromGoogleImageSearch($request->shopName);

        //echo '店名：' . $request->shopName;
        // 取得した値をビュー「book/index」に渡す
        return view('shopImageURL/index', compact('shopImageURL'));
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
}
