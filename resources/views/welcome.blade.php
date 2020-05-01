<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>らんぐるTOP</title>
</head>
<body>

<form action = "/shopSearch" method = "post">
  <input type="hidden" name="_token" value="{{ csrf_token() }}">
  <input id="latitude"  type="hidden" type="text" name="latitude">
  <input id="longitude" type="hidden" type="text" name="longitude">
  <input type = "submit" value ="送信">
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
    <ul id="location">
    </ul>
</body>
</html>