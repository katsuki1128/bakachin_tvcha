<?php

// session_start();

// // データを保存する連想配列がセッションに存在しない場合は初期化する
// if (!isset($_SESSION['data'])) {
//     $_SESSION['data'] = array();
// }

// POST リクエストがあった場合の処理

$registerId = $_POST["register_id"];
$stampName = $_POST["stamp_name"];

// $registerId と $stampName を連想配列に追加する
// $newData = array(
//     "registerId" => $registerId,
//     "stampName" => $stampName
// );

// // 連想配列をセッションに追加する
// $_SESSION['data'][] = $newData;

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

// デバッグ用に連想配列の内容を表示
// print_r($_SESSION['data']);
