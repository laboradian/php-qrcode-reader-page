<?php
require __DIR__ . '/../vendor/autoload.php';

//------------------------
// ヘルパー関数
//------------------------
function e($str)
{
    return htmlspecialchars($str, ENT_QUOTES);
}

//--------------------------------
// セキュリティのためのHTTPヘッダ
//--------------------------------
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge"  >
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <!-- Optional theme -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
  <!--  Font Awesome の CDN  -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
  <!--<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/styles/default.min.css">-->
  <link rel="stylesheet" href="css/main.css">
  <title>QRコード リーダー</title>
</head>

<body>
  <header>
    <h1>QRコード リーダー</h1>
  </header>

  <div class="well">
    <p>QRコードから値を読み取ります。</p>
  </div>

  <div class="panel panel-success">
    <div class="panel-heading">テスト</div>
    <div class="panel-body">
        雛形プロジェクトです。
    </div>
  </div>

  <div class="well">
    <p>ソースコード</p>
    <ul>
        <li><a href="https://github.com/laboradian/php-qrcode-reader-page">laboradian/php-qrcode-reader-page</a></li>
    </ul>

  </div>

  <hr>
  <footer>© 2017 <a href="http://laboradian.com/">Laboradian</a></footer>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
  <!--<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/highlight.min.js"></script>
  <script>hljs.initHighlightingOnLoad();</script>-->
  <script src="js/main.js" charset="utf-8"></script>
</body>
</html>
