<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Geolocation Sample</title>
</head>
<body>

<form action = "/book" method = "post">
  <input type="hidden" name="_token" value="{{ csrf_token() }}">
  <input id="latitude" type="hidden" type="text" name="latitude">
  <input type = "submit" value ="送信/">
</form>


<script type="text/javascript">
    if (navigator.geolocation) {
        // 現在の位置情報取得を実施
        navigator.geolocation.getCurrentPosition(
        // 位置情報取得成功時
        function (pos) { 
                var location = pos.coords.latitude;
                //location += "<li>"+"経度：" + pos.coords.longitude + "</li>";
                document.getElementById("latitude").value = location;
        },
        // 位置情報取得失敗時
        function (pos) { 
                var location ="<li>位置情報が取得できませんでした。</li>";
                document.getElementById("latitude").innerHTML = location;
        });
    } else {
        window.alert("本ブラウザではGeolocationが使えません");
    }
</script>



    <ul id="location">
    </ul>
</body>
</html>