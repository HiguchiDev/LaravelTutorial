$(function() {

    shopImageDispLoading();

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },//Headersを書き忘れるとエラーになる
        url: '/shopSearch/getShopImage/ajax',//ご自身のweb.phpのURLに合わせる
        type: 'GET',//リクエストタイプ
    })
    // Ajaxリクエスト成功時の処理
    .done(function(data) {
        // Laravel内で処理された結果がdataに入って返ってくる
        $('#shopImage').append(data);
    })
    // Ajaxリクエスト失敗時の処理
    .fail(function(data) {
        $('#shopImage').append('お店の画像取得に失敗しました。');
    })
        // 処理終了時
    .always( function(data) {
        // Lading 画像を消す
        shopImageDispRemoveLoading();
    });
    
});

 function shopImageDispLoading(){
    
    //$("#loading").append("{{HTML::image('img/loading.jpg', 'a picture')}}");
    //$("#loading").append(<img src="{{ asset('/image/loading.gif') }}"></img>);
    $("#shopImageloading").append("<img src=/image/loading.gif>");
      
  }
   
  /* ------------------------------
   Loading イメージ削除関数
   ------------------------------ */
  function shopImageDispRemoveLoading(){
    $("#shopImageloading").remove();
  }