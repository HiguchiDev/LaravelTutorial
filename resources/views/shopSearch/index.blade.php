<head>
  <title>ぐるなび　選択結果</title>
  <link rel="stylesheet" href="{{ asset('css/ShopSearchCss.css') }}">
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
  <script type="text/javascript" src="{{ asset('js/ShopSearchCss.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/CalcDistance.js') }}"></script>
</head>
<body>
  <div class = "shopInfo">
  <a href= {{ $shopInfo['url'] }} target="_blank">{{ $shopInfo['name'] }}</a>
  <br>
 
    <div id="distance">
      <div id="loading"></div>
    </div>

    <div class = "shopPicture">
      <img src={{ $shopInfo['image_url']['shop_image1'] }} alt="店舗画像" width="200" height="200" border="0" />
    </div>

    <form action="/shopSearch">
      <button class="nextShopButton" type="submit">次のお店</button>
    </form>

  </div>
  
      <!--<form action="/shopSearch" method = "post">-->
  <form action="/shopImageURL" method = "get">
    <button class="getShopImageButton" type="submit">画像取得</button>
    <input id="shopName" type="hidden" type="text" name="shopName" value={{ $shopInfo['name'] }}>
  </form>

  <div class = "getPositionButtonWrapper">
    <!--<form action="/shopSearch" method = "post">-->
    <form action="/shopSearch" method = "post">
    <button class="getPositionButton" type="submit">位置情報リセット</button>
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input id="latitude"  type="hidden" type="text" name="latitude">
    <input id="longitude" type="hidden" type="text" name="longitude">
    </form>

  <script type="text/javascript">
 
      if (navigator.geolocation) {
          // 現在の位置情報取得を実施
          navigator.geolocation.getCurrentPosition(
          // 位置情報取得成功時
          function (pos) { 
                  var latitude = pos.coords.latitude;
                  var longitude = pos.coords.longitude;

                  document.getElementById("latitude").value = latitude;
                  document.getElementById("longitude").value = longitude;
          },
          // 位置情報取得失敗時
          function (pos) { 
                  var latitude ="<li>緯度情報が取得できませんでした。</li>";
                  var longitude ="<li>経度情報が取得できませんでした。</li>";

                  document.getElementById("latitude").value = latitude;
                  document.getElementById("longitude").value = longitude;
          });
      } else {
          window.alert("本ブラウザではGeolocationが使えません");
      }
  </script>
</div>
</body>