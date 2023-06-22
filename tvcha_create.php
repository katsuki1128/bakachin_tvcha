<?php

// CSVファイルのパス
$csvFile = 'data/submit_counts.csv';

// CSVファイルに書き込むデータを格納する配列
$dataArray = array();

// CSVファイルから既存のデータを読み込む
if (($handle = fopen($csvFile, 'r')) !== false) {
    while (($data = fgetcsv($handle)) !== false) {
        $dataArray[$data[1]] = array($data[0], $data[2]); // Keyと値を配列に追加
    }
    fclose($handle);
}

// ボタンの名前に基づいてカウントを増やす
foreach ($_POST as $name => $value) {
    if (isset($dataArray[$name])) {
        $dataArray[$name][1] += 1; // 既存の行がある場合はカウントを増やす
    } else {
        $dataArray[$name] = array($value, 1); // 新しい行を作成してカウントを初期化
    }
}

// 更新されたデータをCSVファイルに書き込む
if (($handle = fopen($csvFile, 'w')) !== false) {
    foreach ($dataArray as $name => $data) {
        $row = array($data[0], $name, $data[1]);
        fputcsv($handle, $row);
    }
    fclose($handle);
}

// // データ入力画面に移動する
header("Location:tvcha_user.php");
