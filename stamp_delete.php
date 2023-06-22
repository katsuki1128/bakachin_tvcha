<?php

// var_dump($_POST);
// exit();

$filePath = 'data/stamp_list.csv';
$deleteIndex = intval($_POST['delete']);

// ファイルを読み込み
$lines = file($filePath);

echo $deleteIndex;

// 指定した行を削除

unset($lines[$deleteIndex]);

// ファイルを書き込みモードでオープン
$file = fopen($filePath, 'w');

// ファイルに残りの行を書き込む
fwrite($file, implode('', $lines));

// ファイルを閉じる
fclose($file);

// データ入力画面に移動する
header("Location:tvcha_user.php");
