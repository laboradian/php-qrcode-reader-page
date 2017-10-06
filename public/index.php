<?php
require __DIR__ . '/../vendor/autoload.php';

//ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');
//error_reporting(-1);

//------------------------
// ヘルパー関数
//------------------------
function e($str)
{
    return htmlspecialchars($str, ENT_QUOTES);
}

/**
 * URLで指定された画像ファイルのQRコードから値を取得して返す。
 * @param string $fileurl
 * @return string || false QRコードから取得した文字列（何も取得できない場合はfalseが返る）
 * @throws Exception
 */
function getStringFromUrl($fileurl) {

    $temp_file_path = tempnam(sys_get_temp_dir(), 'qrcode');
    $tmpfile = null;

    $file = fopen($fileurl, 'rb');
    if ($file) {
        $tmpfile = fopen($temp_file_path, 'wb');
        if ($tmpfile) {
            while(!feof($file)) {
                fwrite($tmpfile, fread($file, 1024 * 8), 1024 * 8);
            }
        }
    }

    // 画像ファイルであるかチェックする
    if (\App\Utils::isValidImageFile($temp_file_path) === false) {
        throw(new Exception('不正なファイルです。'));
    }

    $qrcode = new \QrReader($temp_file_path);

    if ($file) {
        fclose($file);
    }
    if ($tmpfile) {
        fclose($tmpfile);
    }

    return $qrcode->text();
}

/**
 * アップロードされた画像ファイルのQRコードから値を取得して返す。
 * @param  array $files $_FILESそのものが渡される
 * @return string || false QRコードから取得した文字列（何も取得できない場合はfalseが返る）
 * @throws Exception
 */
function getStringFromQRCode($files) {

    $filepath = $files['userfile']['tmp_name'];

    if (($files['userfile']['error']) !== 0) {
        throw(new Exception('アップロードファイルのエラーです。(' . $files['userfile']['error'] . ')'));
    }

    // ファイルの存在チェック
    if (!file_exists($filepath)) {
        throw(new Exception('ファイルを取得することができませんでした。'));
    }

    // 画像ファイルであるかチェックする
    if (\App\Utils::isValidImageFile($filepath) === false) {
        throw(new Exception('不正なファイルです。'));
    }

    $qrcode = new \QrReader($filepath);
    return $qrcode->text();
}

//-----------
// 変数
//-----------
$text = ''; // QRコードから取得した文字列
$fileurl = '';
$error_messages = [];

if (isset($_POST['MAX_FILE_SIZE'])) {
    //var_dump($_POST);
    //var_dump($_FILES);

    try {

        // URL を使う場合
        if ($_POST['fileurl'] !== '') {
            $fileurl = $_POST['fileurl'];
            $text = getStringFromUrl($fileurl);
        // アップロードファイルを使う場合
        } else {
            $text = getStringFromQRCode($_FILES);
        }
        if ($text === '' || $text === false) {
            $error_messages[] = '値が取得できませんでした。';
        }

    } catch (\Exception $e) {

        $error_messages[] = $e->getMessage();

    }

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

  <div class="panel panel-success">
    <div class="panel-heading">QRコードから値を読み取ります。</div>
    <div class="panel-body">

        <!-- データのエンコード方式である enctype は、必ず以下のようにしなければなりません -->
        <form enctype="multipart/form-data" method="POST">
            <!-- MAX_FILE_SIZE は、必ず "file" input フィールドより前になければなりません -->
            <input type="hidden" name="MAX_FILE_SIZE" value="300000" />
            <p class="instruction">
                (1) QRコードの画像ファイルを選択してください。
            </p>
            <!-- input 要素の name 属性の値が、$_FILES 配列のキーになります -->
            <input name="userfile" type="file" />
            <p class="fileurl-title">URLを使ってファイルを指定することもできます。
                <input type="text" name="fileurl" style="width:300px"
                       placeholder="例：http://example.com/image.png"
                       value="<?php if ($fileurl !== '') echo e($fileurl); ?>"></p>

            <p class="instruction">
                (2) 以下のボタンをクリックしてファイルを送信してください。
            </p>
            <input type="submit" value="ファイルを送信" />
        </form>

        <p class="notice-title">注意</p>
        <ul>
            <li>ファイルサイズは、300KB以内にしてください。</li>
            <li>アップロードされたファイルは特に保存したりはしていません。</li>
        </ul>

        <?php if ($text !== '' && $text !== false): ?>
        <p class="result-title">取得した文字列</p>
        <section class="result">
            <div class="alert alert-info" role="alert">
                <?= e($text) ?>
            </div>
        </section>
        <?php endif; ?>

        <?php if (count($error_messages) > 0): ?>
            <div class="alert alert-danger" role="alert">
                <ul>
                <?php foreach($error_messages as $msg): ?>
                    <li><?= e($msg); ?></li>
                <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

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
