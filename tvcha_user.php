<?php

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




// ⭐️４番目の挙動 => スマホ上のボタンを追加
// データまとめ用の空文字変数
$displayTable = '';
// ボタンを追加するための変数
$buttonHTML = '';

// ファイルを開く（読み取り専用）
$fileDisplayPath = 'data/stamp_display.csv';
if (file_exists($fileDisplayPath)) {
    $fileDisplay = fopen($fileDisplayPath, 'r');

    // ファイルを1行ずつ読み込み、ボタンを追加
    while (($displayLine = fgets($fileDisplay)) !== false) {
        // 改行コードを除去してデータを取得
        $displayData = rtrim($displayLine);
        // カンマで分割して配列に格納
        $displayValues = explode(',', $displayData);

        // 最初の要素をname属性に、次の要素をvalue属性に設定したボタンを追加
        $buttonHTML .= "<div>";
        $buttonHTML .= "<img src=\"img/{$displayValues[0]}.png\"><br>";
        $buttonHTML .= "<input type=\"submit\" value=\"{$displayValues[1]}\"name=\"{$displayValues[0]}\">";
        $buttonHTML .= "</div>";
    }

    // ファイルを閉じる
    fclose($fileDisplay);
}




// ⭐️６番目の挙動  =>  クリックされたCSVファイルを読み込んで配列に変換、jsonにする

$csvChart = array();

$filePath = 'data/submit_counts.csv';
if (file_exists($filePath)) {
    $fileChart = fopen($filePath, 'r');
    // ファイルの読み込み処理などを実行

    if ($fileChart) {
        for ($i = 0; ($row = fgetcsv($fileChart)) !== false; $i++) {
            $csvChart[$i] = $row;
        }
        fclose($fileChart);
    }
}
// 配列をJSONに変換する
$jsonChart = json_encode($csvChart);

?>

<!DOCTYPE html>
<html lang="ja">


<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>スタンプ作成画面</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="reset.css">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

</head>

<body>


    <!-- ⭐️ここが大元！スタンプ生成ブロック -->
    <div id="setting">
        <form action="stamp_create.php" method="POST">
            <div>
                <div>
                    <div class="regi_wrapper">
                        <div class="regi_item">
                            登録ID
                        </div>
                        <div class="regi_form">
                            <input type="text" name="register_id" pattern="[0-9a-zA-Z]{2}" title="2桁の半角英数字を入力してください" inputmode="verbatim">
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
                </div>
                <div>
                    <button type="submit" id="resiter_button">登録</button>
                </div>
            </div>
        </form>
    </div>

    <!-- ⭐️登録したスタンプを表示するエリア -->
    <form>
        <div id="stamp_list">
            <!-- <h3>登録されたスタンプ</h3> -->
            <div id="stamp_list_table">

            </div>
        </div>
    </form>

    <!-- ⭐️円グラフ表示エリア -->
    <div id="chart_wrapper">
        <canvas id="myChart"></canvas>
    </div>

    <!-- ⭐️５番目の挙動 ユーザーに表示するエリア tvcha_create.phpにPOST-->
    <div id="question">
        <h2>福岡の名物といえば？</h2>

        <form action="tvcha_create.php" method="POST">
            <div id="button_area">
                <?= $buttonHTML ?>
            </div>
        </form>
    </div>



    <script>
        // ⭐️３番目の挙動 => jsonからスタンプリストを作成
        const stampList = <?= $json ?>;
        console.log(stampList);

        // テーブルのヘッダ行を作成
        let tableHTML = "<table>\n";
        // stampListのデータをテーブルに追加
        for (let i = 0; i < stampList.length; i++) {
            let stamp = stampList[i];
            tableHTML += "<tr>";
            tableHTML += "<td class='column1'>" + stamp[0] + "</td>";
            tableHTML += "<td class='column2'>" + stamp[1] + "</td>";
            tableHTML += "<td class='column3'><button name='generate' value='" + i + "'formmethod='POST' formaction='stamp_display.php'>生成</button></td>";
            tableHTML += "<td class='column3'><button name='delete' value='" + i + "' formmethod='POST' formaction='stamp_delete.php'>削除</button></td>";
            tableHTML += "</tr>\n";
        }

        // テーブルを閉じる
        tableHTML += "</table>\n";

        // テーブルをHTMLに追加
        $("#stamp_list_table").html(tableHTML);

        // ⭐️７番目の挙動 => jsonからスタンプリストを作成
        const chart = <?= $jsonChart ?>;
        console.log(chart);


        // ⭐️８番目の挙動 円グラフを作る
        // データの取得
        const jsonData = JSON.parse('<?php echo $jsonChart; ?>');
        const data = jsonData.map(item => item[2]); // 数値部分を抽出

        // ラベルの取得
        const labels = jsonData.map(item => item[0]); // ラベル部分を抽出

        // 色の設定
        const colors = ['#687c8d', '#96abbd', '#e9e9e9', '#c5bfb9', '#948f89', '#000000']; // 色の配列

        // チャートの描画
        const ctx = $('#myChart')[0].getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors.slice(0, data.length) // 必要な分だけ色を使用する

                }]

            },
            options: {
                plugins: {
                    datalabels: {
                        color: '#242424', // データラベルのテキスト色
                        font: {
                            size: 16 // データラベルのフォントサイズ
                        },
                        formatter: (value, context) => {
                            const label = context.chart.data.labels[context.dataIndex];
                            return label + ': ' + value;
                        },
                        display: true
                    },
                    legend: {

                    }
                }
            },
            plugins: [
                ChartDataLabels,
            ]
        });

        // 生成ボタンを押したら、中間テーブルを作成
        // $("button[name='generate']").click(function() {
        // let btnVal = $(this).val(); // ボタンの値を取得
        // let element = stampList[index]; // JSONデータから対応する要素を取得

        // console.log(btnVal);

        // 要素の値を取得
        // let value1 = element[0];
        // let value2 = element[1];

        // Ajaxリクエストを送信
        // $.ajax({
        //     url: "stamp_display.php",
        //     type: "POST",
        //     data: {
        //         value1: value1,
        //         value2: value2
        //     },
        //     success: function(response) {
        //         // リクエスト成功時の処理
        //         console.log("POST成功:", response);
        //     },
        //     error: function(xhr, status, error) {
        //         // リクエストエラー時の処理
        //         console.error("POSTエラー:", error);
        //     }
        // });
        // });


        // // 「生成」ボタンを押したらスマホ内にスタンプを表示
        // $("button[name='generate']").click(function() {
        //     // ボタンの値を取得
        //     let index = $(this).val();

        //     // JSONデータから対応する要素を取得
        //     let element = stampList[index];

        //     // 要素の値を取得
        //     let value = element[1];

        //     let input = $("<input>").attr({
        //         type: "submit",
        //         value: value,
        //         name: "YY"
        //     });

        //     // ボタンを追加する要素を取得
        //     let buttonArea = $("#button_area");

        //     // ボタンを追加
        //     input.appendTo(buttonArea);

        //     // ボタンを無効化
        //     $(this).prop("disabled", true);
        // });
    </script>




</body>

</html>