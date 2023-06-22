<?php

$registerId = $_POST["register_id"];
$stampName = $_POST["stamp_name"];

// データ1件を1行にまとめる（最後に改行を入れる）
$write_data = "{$registerId},{$stampName} \n";

// ファイルを開く
$file = fopen('data/stamp_list.csv', 'a');

// ファイルをロックする
flock($file, LOCK_EX);

// 指定したファイルに指定したデータを書き込む
fwrite($file, $write_data);

// ファイルのロックを解除する
flock($file, LOCK_UN);

// ファイルを閉じる
fclose($file);

// データ入力画面に移動する
header("Location:tvcha_user.php");
