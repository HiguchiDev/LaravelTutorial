<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopImage extends Model
{

    protected $fillable = [
        'name', 'adress', 'shop_image_id',
    ];

    public static function insertShopImage($shopName, $shopAdress, $shopImageURL) {
        
        /***************************ここは別途コントローラ切り出すべきかも***************************/
        if(empty($shopImageURL)){
            $shopImageURL = ShopImage::getShopImageFromGoogleImageSearch($shopName);
            
        }
        
        
        $img = file_get_contents($shopImageURL);
        
        $img_name = ShopImage::max('shop_image_id');
        
        if (empty($img_name)) {
            $img_name = 1;
        }
        else{
            $img_name++;
        }
    
        //画像を保存
        file_put_contents(public_path() . '\\image\\shopImages\\' . $img_name . '.jpg', $img);
        \Log::info($shopImageURL);
        //\Log::info($img);
        \Log::info($img_name);

        /***************************ここまで***************************/

        ShopImage::create([
            'name' => $shopName,
            'adress' => $shopAdress,
            'shop_image_id' => $img_name
            ]);

    }

    private static function getShopImageFromGoogleImageSearch($word){

        
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
