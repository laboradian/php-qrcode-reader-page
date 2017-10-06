<?php
namespace App;

class Utils {

    /**
     * 指定されたファイルが画像ファイルであるかどうかを判定する。
     * @param String $filepath ファイルパス（拡張子は当てにしない）
     * @return bool
     */
    public static function isValidImageFile($filepath)
    {
        try {
            // WARNING, NOTICE が発生する可能性あり
            $img_info = getimagesize($filepath);

            switch ($img_info[2]) {
            case IMAGETYPE_GIF:
            case IMAGETYPE_JPEG:
            case IMAGETYPE_PNG:
                // イメージリソースが生成できるかどうかでファイルの中身を判定する。
                // データに問題がある場合、WARNING が発生する可能性あり
                if (imagecreatefromstring(file_get_contents($filepath)) !== false) {
                    return true;
                }
            }

        } catch (\ErrorException $e) {

            // ログ出力する文字列の例
            $err_msg = sprintf("%s(%d): %s (%d) filepath = %s",
                __METHOD__, $e->getLine(), $e->getMessage(), $e->getCode(), $filepath);

            // TODO:
            //   - $e->getSeverity() の値によって、ログ出力を変えたりする。

        }

        return false;
    }

    /**
     * URLからファイルを取得し、ローカルに保存する。
     * @param string $url  取得するファイルを表すURL
     * @param string $path 保存先のファイルパス
     */
    public static function downloadFile($url, $path)
    {
        $newfname = $path;
        $file = fopen ($url, 'rb');
        if ($file) {
            $newf = fopen ($newfname, 'wb');
            if ($newf) {
                while(!feof($file)) {
                    fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
                }
            }
        }
        if ($file) {
            fclose($file);
        }
        if ($newf) {
            fclose($newf);
        }
    }

}
