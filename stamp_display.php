<?php

// var_dump($_POST);
// exit();

$fileFromPath = 'data/stamp_list.csv';
$fileToPath = 'data/stamp_display.csv';
$generateIndex = $_POST['generate'];

// ファイルから要素を読み込み
$lines = file($fileFromPath);

// // 抽出したい要素を指定して新しい行を作成
$extractedElement = $lines[$generateIndex];

// // ファイルを書き込みモードでオープン
$file = fopen($fileToPath, 'a');

// // ファイルに残りの行を書き込む
fwrite($file, $extractedElement);

// // ファイルを閉じる
fclose($file);

// // データ入力画面に移動する
header("Location:tvcha_user.php");
