<?php

// セッションの開始
session_start();

// ボタンが押された回数の初期値をセッション変数に格納



if (!isset($_SESSION["counter01"])) {
    $_SESSION["counter01"] = 0;
}
if (!isset($_SESSION["counter02"])) {
    $_SESSION["counter02"] = 0;
}
if (!isset($_SESSION["counter03"])) {
    $_SESSION["counter03"] = 0;
}
if (!isset($_SESSION["counter04"])) {
    $_SESSION["counter04"] = 0;
}





if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ボタンが押されたかどうかをチェック
    if (isset($_POST["choice01"])) {
        // 明太子ボタンが押された場合、カウンタを１増やす
        $_SESSION["counter01"]++;
    }
    if (isset($_POST["choice02"])) {
        // うどんボタンが押された場合、カウンタを１増やす
        $_SESSION["counter02"]++;
    }
    if (isset($_POST["choice03"])) {
        // ラーメンボタンが押された場合、カウンタを１増やす
        $_SESSION["counter03"]++;
    }
    if (isset($_POST["choice04"])) {
        // 屋台ボタンが押された場合、カウンタを１増やす
        $_SESSION["counter04"]++;
    }
}


// データの準備

$totalCount = $_SESSION["counter01"] + $_SESSION["counter02"] + $_SESSION["counter03"] + $_SESSION["counter04"];

$data = "明太子派," . $_SESSION["counter01"] .  "," . number_format($_SESSION["counter01"] / $totalCount * 100, 1) . "%" . PHP_EOL;
$data .= "うどん派," . $_SESSION["counter02"] .  "," . number_format($_SESSION["counter02"] / $totalCount * 100, 1) . "%" . PHP_EOL;
$data .= "ラーメン派," . $_SESSION["counter03"] .  "," . number_format($_SESSION["counter03"] / $totalCount * 100, 1) . "%" . PHP_EOL;
$data .= "屋台派," . $_SESSION["counter04"] .  "," . number_format($_SESSION["counter04"] / $totalCount * 100, 1) . "%" . PHP_EOL;


// ファイルにデータを追記
$file = fopen("data/click.csv", "w+");
flock($file, LOCK_EX);
fwrite($file, $data);
flock($file, LOCK_UN);
fclose($file);
header("Location:tvcha_user.php");

// // カウンタの値を出力
echo "明太子派: " . $_SESSION["counter01"] . "<br>";
echo "うどん派: " . $_SESSION["counter02"] . "<br>";
echo "ラーメン派: " . $_SESSION["counter03"] . "<br>";
echo "屋台派: " . $_SESSION["counter04"] . "<br>";



// if (!isset($_SESSION["counter01"])) {
//     $_SESSION["counter01"] = 0;
// }
// if (!isset($_SESSION["counter02"])) {
//     $_SESSION["counter02"] = 0;
// }
// if (!isset($_SESSION["counter03"])) {
//     $_SESSION["counter03"] = 0;
// }
// if (!isset($_SESSION["counter04"])) {
//     $_SESSION["counter04"] = 0;
// }

// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     // ボタンが押されたかどうかをチェック
//     if (isset($_POST["choice01"])) {
//         // 明太子ボタンが押された場合、カウンタを１増やす
//         $_SESSION["counter01"]++;
//     }
//     if (isset($_POST["choice02"])) {
//         // うどんボタンが押された場合、カウンタを１増やす
//         $_SESSION["counter02"]++;
//     }
//     if (isset($_POST["choice03"])) {
//         // ラーメンボタンが押された場合、カウンタを１増やす
//         $_SESSION["counter03"]++;
//     }
//     if (isset($_POST["choice04"])) {
//         // 屋台ボタンが押された場合、カウンタを１増やす
//         $_SESSION["counter04"]++;
//     }
// }


// $counterNames = ["counter01", "counter02", "counter03", "counter04"];
// $i = 0;

// while ($i < count($counterNames)) {
//     if (!isset($_SESSION[$counterNames[$i]])) {
//         $_SESSION[$counterNames[$i]] = 0;
//     }
//     $i++;
// }

// $i = 0; // $i の初期化

// // POSTデータの取得
// while ($i < count($counterNames)) {
//     $choiceName = "choice" . sprintf("%02d", $i + 1);
//     if (isset($_POST[$choiceName])) {
//         $_SESSION[$counterNames[$i]]++;
//     }
//     $i++;
// }


// $write_data = "明太子{$counter01}うどん{$counter02}\n";

// $file = fopen("data/todo.txt", "a");
// flock($file, LOCK_EX);
// fwrite($file, $write_data);
// flock($file, LOCK_UN);
// fclose($file);
// header("Location:tvcha_user.php");

// カウンタの値を出力
// echo $write_data;
