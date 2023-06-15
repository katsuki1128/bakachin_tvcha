<?php

// データまとめ用の空文字変数
$clickTable = '';

// ファイルを開く（読み取り専用）
$file01 = fopen('data/click.csv', 'r');
// ファイルをロック
flock($file01, LOCK_EX);

// テーブルのヘッダ行を追加
$clickTable .= "<table>\n";

// ファイルを1行ずつ読み込み、テーブルの行を追加
while (($clickLine = fgets($file01)) !== false) {
    // 改行コードを除去してデータを取得
    $clickData = rtrim($clickLine, PHP_EOL);
    // カンマで分割して配列に格納
    $clickValues = explode(',', $clickData);
    // テーブルの行を追加
    $clickTable .= "<tr>";
    foreach ($clickValues as $clickValue) {
        $clickTable .= "<td>{$clickValue}</td>";
    }
    $clickTable .= "</tr>\n";
}

// テーブルを閉じる
$clickTable .= "</table>\n";

// ロックを解除する
flock($file01, LOCK_UN);
// ファイルを閉じる
fclose($file01);




// 作ったスタンプのリストを表示する変数
$stampListTable = '';

// スタンプリストを呼び出す関数
$filePath = 'data/stamp_list.csv';

// ファイルが存在する場合にのみ処理を実行
if (file_exists($filePath)) {

    // ファイルを開く（読み取り専用）
    $fileStampList = fopen('data/stamp_list.csv', 'r');

    // テーブルのヘッダ行を追加
    $stampListTable .= "<table>\n";

    // 行数を取得
    $lineCount = count(file($filePath));

    // ファイルを1行ずつ読み込み、テーブルの行を追加
    for ($i = 0; $i < $lineCount; $i++) {
        // 行を読み込む
        $stampLine = fgets($fileStampList);
        // 改行コードを除去してデータを取得
        $stampData = rtrim($stampLine, PHP_EOL);
        // カンマで分割して配列に格納
        $stampValues = explode(',', $stampData);
        // テーブルの行を追加
        $stampListTable .= "<tr>";
        $stampListTable .= "<td class='column1'>{$stampValues[0]}</td>";
        $stampListTable .= "<td class='column2'>{$stampValues[1]}</td>";
        // 「生成」ボタンと「削除」ボタンを追加
        $stampListTable .= "<td class='column3'><button id='generate$i'>生成</button></td>";
        $stampListTable .= "<td class='column3'><button>削除</button></td>";

        // テーブルを閉じる
        $stampListTable .= "</tr>\n";
    }

    // テーブルを閉じる
    $stampListTable .= "</table>\n";

    // ファイルを閉じる
    fclose($fileStampList);
}

// ⭐️２番目の挙動  =>  CSVファイルを読み込んで配列に変換、jsonにする
$csvData = array();
$file = fopen('data/stamp_list.csv', 'r');
if ($file) {
    for ($i = 0; ($row = fgetcsv($file)) !== false; $i++) {
        $csvData[$i] = $row;
    }
    fclose($file);
}

// 配列をJSONに変換する
$json = json_encode($csvData);

// $rowToRead = 1; // 読み込む行の番号
// $stampId = ''; // グローバル変数として宣言

// $file = fopen($filePath, 'r');
// if ($file) {
//     $rowCounter = 0;
//     while (($line = fgets($file)) !== false) {
//         $rowCounter++;
//         if ($rowCounter == $rowToRead) {
//             $data = explode(',', $line);
//             $stampId = trim($data[0]);
//             $stampName = trim($data[1]);
//             $html = '<input type="submit" value="' . htmlspecialchars($stampName) .
//                 '"
//                     name = "' . htmlspecialchars($stampId) . '" > ';

//             break;
//         }
//     }
//     fclose($file);
// }

?>

<!DOCTYPE html>
<html lang="ja">


<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>スタンプ作成画面</title>

    <link rel="stylesheet" href="style.css">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>


</head>

<body>
    <div id="question">
        <h2>福岡の名物といえば？</h2>

        <form action="tvcha_create.php" method="POST">
            <div id="button_area">
                <input type="submit" value="明太子" name="choice01">
                <input type="submit" value="うどん" name="choice02">
                <input type="submit" value="ラーメン" name="choice03">
                <input type="submit" value="屋台" name="choice04">
            </div>
        </form>


        <h3>スタンプの押された数とパーセンテージ</h3>
        <div id="click_table">
            <?= $clickTable ?>
        </div>
    </div>

    <!-- ⭐️ここが大元！スタンプ生成ブロック -->
    <div id="setting">
        <form action="stamp_create.php" method="POST">
            <div>
                <div class="regi_wrapper">
                    <div class="regi_item">
                        登録ID
                    </div>
                    <div class="regi_form">
                        <input type="text" name="register_id" pattern="[0-9a-zA-Z]{2}" title="2桁の半角英数字を入力してください">
                    </div>
                </div>
                <div class="regi_wrapper">
                    <div class="regi_item">
                        スタンプ名
                    </div>
                    <div class="regi_form">
                        <input type="text" name="stamp_name">
                    </div>
                </div>
                <div>
                    <button type="submit">登録</button>
                </div>
            </div>
        </form>


        <!-- 登録したスタンプを表示するエリア -->
        <div id="stamp_list">
            <!-- <h3>登録されたスタンプ</h3> -->
            <div id="stamp_list_table">
                <?= $stampListTable ?>
            </div>
        </div>
    </div>
    <script>
        let rowToRead
        $("button[id^='generate']").click(function() {
            let buttonId = $(this).attr("id");
            rowToRead = buttonId.substr(buttonId.indexOf("generate") + 8);
            console.log(buttonId, rowToRead);
        });

        const stampList = <?= $json ?>;
        console.log(stampList);

        // $(document).ready(function() {
        //     let choiceCount = 0; // 変数をグローバルスコープで宣言

        //     function countButtonClick() {
        //         if ($(this).attr('id') === 'registerButton') {
        //             choiceCount = parseInt($('#choiceCount').val()) + 1;
        //             $('#choiceCount').val(choiceCount);
        //             console.log(choiceCount);
        //         }
        //     }

        //     // 登録ボタンがクリックされたらカウント関数を実行
        //     $('button[type="submit"]').click(countButtonClick);
        // });

        // <div>
        //     deadline: <input type="date" name="deadline">
        // </div>Z

        // <div>
        //     <button>submit</button>
        // </div>
    </script>


</body>

</html>