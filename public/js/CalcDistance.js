$(function() {

    dispLoading();

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },//Headersを書き忘れるとエラーになる
        url: '/shopSearch/distance/ajax',//ご自身のweb.phpのURLに合わせる
        type: 'GET',//リクエストタイプ
    })
    // Ajaxリクエスト成功時の処理
    .done(function(data) {
        // Laravel内で処理された結果がdataに入って返ってくる
        $('#distance').append(data);
    })
    // Ajaxリクエスト失敗時の処理
    .fail(function(data) {
        $('#distance').append('お店との距離取得に失敗しました。');
    })
        // 処理終了時
    .always( function(data) {
        // Lading 画像を消す
        removeLoading();
    });
    
});

/* ------------------------------
 Loading イメージ表示関数
 引数： msg 画面に表示する文言
 ------------------------------ */
 function dispLoading(){
    
    //$("#loading").append("{{HTML::image('img/loading.jpg', 'a picture')}}");
    //$("#loading").append(<img src="{{ asset('/image/loading.gif') }}"></img>);
    $("#loading").append("<img src=/image/loading.gif>");
      
  }
   
  /* ------------------------------
   Loading イメージ削除関数
   ------------------------------ */
  function removeLoading(){
    $("#loading").remove();
  }