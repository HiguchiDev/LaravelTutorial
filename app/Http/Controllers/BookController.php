<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Book;

class BookController extends Controller
{
    /*public function grabIpInfo($ip)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://api.ipgeolocationapi.com/geolocate/" . $ip);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $returnData = curl_exec($curl);

        curl_close($curl);

        return $returnData;
    }
    */
    public function index()
    {
        //https://api.gnavi.co.jp/RestSearchAPI/v3/?keyid=(発行されたアクセスキー）&range=1&sort=2
        /*$ReqURL = 'https://api.gnavi.co.jp/RestSearchAPI/v3/?keyid=2e49b5d121c0f40773b048f7b7d403f8&name=山さわ';

        //file_get_contentsでレスポンスを処理
        $json = file_get_contents($ReqURL);
        //JSONをデコード
        $json_value = json_decode($json, true);

        var_dump($json_value['rest'][0]['update_date']); 
        */
        //************************************* */

        /*
        $ipInfo = $this->grabIpInfo($_SERVER["REMOTE_ADDR"]);
        $ipJsonInfo = json_decode($ipInfo);

        var_dump($ipJsonInfo);
*/
        
        //************************************* */
        // DBよりBookテーブルの値を全て取得
        $books = Book::all();

        // 取得した値をビュー「book/index」に渡す
        return view('book/index', compact('books'));
    }

    public function edit($id)
    {
        // DBよりURIパラメータと同じIDを持つBookの情報を取得
        $book = Book::findOrFail($id);

        // 取得した値をビュー「book/edit」に渡す
        return view('book/edit', compact('book'));
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);
        $book->name = $request->name;
        $book->price = $request->price;
        $book->author = $request->author;
        $book->save();

        return redirect("/book");
    }

    public function create(Request $request)
    {
        $book = new Book();

        return view('book/create', compact('book'));
    }

    public function store(Request $request)
    {
        /*
        $book = new Book();
        $book->name = $request->name;
        $book->price = $request->price;
        $book->author = $request->author;
        $book->save();
        */
        //echo "a";
        //echo $request->latitude;

        //var_dump($request->latitude);
        return redirect("/book");
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();

        return redirect("/book");
    }
}
