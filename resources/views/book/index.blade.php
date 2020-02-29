@extends('book/layout')
@section('content')
<div class="container ops-main">
<div class="row">
  <div class="col-md-12">
    <h3 class="ops-title">Books</h3>
  </div>
</div>
<div class="row">
  <div class="col-md-11 col-md-offset-1">
    <table class="table text-center">
      <tr>
        <th class="text-center">ID</th>
        <th class="text-center">書籍名</th>
        <th class="text-center">価格</th>
        <th class="text-center">著者</th>
        <th class="text-center">削除</th>
      </tr>
      @foreach($books as $book)
      <tr>
        <td>
          <a href="/book/{{ $book->id }}/edit">{{ $book->id }}</a>
        </td>
        <td>{{ $book->name }}</td>
        <td>{{ $book->price }}</td>
        <td>{{ $book->author }}</td>
        <td>
          <form action="/book/{{ $book->id }}" method="post">
            <input type="hidden" name="_method" value="DELETE">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <button type="submit" class="btn btn-xs btn-danger" aria-label="Left Align"><span class="glyphicon glyphicon-trash"></span></button>
          </form>
        </td>
      </tr>
      @endforeach
    </table>
    <div><a href="/book/create" class="btn btn-default">新規作成</a></div>
  </div>
</div>
@endsection

<!--
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Geolocation Sample</title>
</head>
<body>
<script type="text/javascript">
    if (navigator.geolocation) {
        // 現在の位置情報取得を実施
        navigator.geolocation.getCurrentPosition(
        // 位置情報取得成功時
        function (pos) { 
                var location ="<li>"+"緯度：" + pos.coords.latitude + "</li>";
                location += "<li>"+"経度：" + pos.coords.longitude + "</li>";
                document.getElementById("location").innerHTML = location;
        },
        // 位置情報取得失敗時
        function (pos) { 
                var location ="<li>位置情報が取得できませんでした。</li>";
                document.getElementById("location").innerHTML = location;
        });
    } else {
        window.alert("本ブラウザではGeolocationが使えません");
    }
</script>
    <ul id="location">
    </ul>
</body>
</html>
-->