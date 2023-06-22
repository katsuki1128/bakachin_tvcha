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
    <form>
        <fieldset>
            <legend>スタンプ登録画面</legend>
            <div>img: <input type="file" id="img" /></div>
            <div>point: <input type="text" id="point" /></div>
            <div>
                <button type="button" id="send">send</button>
            </div>
        </fieldset>
    </form>

    <p id="output">ここ</p>

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
    <!-- <div id="question">
        <h2>福岡の名物といえば？</h2>

        <form action="tvcha_create.php" method="POST">
            <div id="button_area">

            </div>
        </form>
    </div> -->



    <script type="module">
        // 時刻変換関数

        function convertTimestampToDatetime(timestamp) {
            const _d = timestamp ? new Date(timestamp * 1000) : new Date();
            const Y = _d.getFullYear();
            const m = (_d.getMonth() + 1).toString().padStart(2, "0");
            const d = _d.getDate().toString().padStart(2, "0");
            const H = _d.getHours().toString().padStart(2, "0");
            const i = _d.getMinutes().toString().padStart(2, "0");
            const s = _d.getSeconds().toString().padStart(2, "0");
            return `${Y}/${m}/${d} ${H}:${i}:${s}`;
        }

        //----------------------------------------
        // ▼firebaseプロジェクトとjavaScriptを連携させる
        //----------------------------------------

        // 必要な機能をSDKからインポート
        import {
            initializeApp
        } from "https://www.gstatic.com/firebasejs/9.22.2/firebase-app.js";


        // firebaseコレクションとやり取りをする設定
        import {
            getFirestore,
            collection,
            addDoc,
            serverTimestamp,
            query,
            orderBy, //データのソート
            onSnapshot, // Firestore 上に保存されているデータを取得して console に出力
            doc,
            deleteDoc,

        } from "https://www.gstatic.com/firebasejs/9.22.2/firebase-firestore.js";

        import {
            getStorage,
            ref,
            uploadBytes,
            getDownloadURL
        } from "https://www.gstatic.com/firebasejs/9.22.2/firebase-storage.js";

        // ウェブアプリのFirebaseの設定
        const firebaseConfig = {
            apiKey: "",
            authDomain: "tvcha-9cae7.firebaseapp.com",
            projectId: "tvcha-9cae7",
            storageBucket: "tvcha-9cae7.appspot.com",
            messagingSenderId: "866848033597",
            appId: "1:866848033597:web:c6887382eb14ee58351354",

        };

        // Firebaseの初期化
        const app = initializeApp(firebaseConfig);

        // CloudStorageの初期化
        const storage = getStorage(app);

        // dbに対してデータの追加や取得ができるようにする
        const db = getFirestore(app);

        // 🔽 データ取得条件の指定（今回は時間の新しい順に並び替えて取得）

        const q = query(collection(db, "tvcha"), orderBy("time", "desc"));


        //----------------------------------------
        // ▼送信ボタンクリック時にデータを送信する処理を実装
        //----------------------------------------

        $("#send").on("click", function() {
            const imgFile = $("#img")[0].files[0]; // 選択された画像ファイルを取得

            // Firebase Storage に画像をアップロード
            const storageRef = ref(storage, 'images/' + imgFile.name);
            uploadBytes(storageRef, imgFile)
                .then((snapshot) => {
                    return getDownloadURL(snapshot.ref);
                })
                .then((downloadURL) => {
                    console.log('ダウンロード URL:', downloadURL);

                    const postData = {
                        img: downloadURL, // ダウンロード URL を Firestore の 'img' フィールドに保存
                        point: $("#point").val(),
                        time: serverTimestamp(),
                    };

                    addDoc(collection(db, "tvcha"), postData)
                        .then(() => {
                            console.log('データを Firestore に保存しました');
                            $("#img,#point").val(""); // フォームをリセット
                        })
                        .catch((error) => {
                            console.error('データの保存中にエラーが発生しました', error);
                        });
                })
                .catch((error) => {
                    console.error('画像のアップロード中にエラーが発生しました', error);


                });
        });

        // 画像のダウンロード URL を取得して表示するための関数
        function displayImage(downloadURL, element) {
            const img = document.createElement('img');
            img.src = downloadURL;
            img.alt = 'Image';
            element.appendChild(img);
        }

        // データ取得処理(データベース上でデータの変更が発生したタイミングで {} 内の処理を実行)
        onSnapshot(q, (querySnapshot) => {
            const documents = [];
            querySnapshot.docs.forEach(function(doc) {
                const document = {
                    id: doc.id,
                    data: doc.data(),

                };
                documents.push(document);
            });

            // ドキュメントを取得して配列に格納
            const dataArray = documents.map((document, index) => ({
                id: index,
                ...document.data
            }));
            console.log(dataArray);

            let tableRows = '';
            documents.forEach(function(document, index) {
                const idFormatted = String(dataArray[index].id).padStart(3, '0');
                const deleteButton = `<button class="delete-btn" data-id="${document.id}">削除</button>`;
                tableRows += `
                    <tr>
                        <td>${idFormatted}</td>
                        <td><div class="image_thumnail" id="image-${index}"></div></td>
                        <td>${document.data.point}</td>
                        <td>${convertTimestampToDatetime(document.data.time.seconds)}</td>
                        <td>${deleteButton}</td> 
                    </tr>
                    `;

                // 画像のダウンロード URL を取得して表示
                getDownloadURL(ref(storage, document.data.img))
                    .then((downloadURL) => {
                        const imageElement = $(`#image-${index}`)[0];
                        displayImage(downloadURL, imageElement);
                    })
                    .catch((error) => {
                        console.error('画像のダウンロード中にエラーが発生しました', error);
                    });
            });

            const table = `
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>画像URL</th>
                        <th>ポイント</th>
                        <th>作成日時</th>
                        <th>削除</th>
                    </tr>
                    </thead>
                    <tbody>
                    ${tableRows}
                    </tbody>
                </table>
                `;
            $("#output").html(table);
        });

        //----------------------------------------
        // ▼Firebaseの行を削除・Firebaseの該当するドキュメントを参照し、削除操作を実行
        //----------------------------------------

        // function deleteDoc(documentId) {

        //     // Firestoreの参照を取得
        //     const docRef = db.collection("tvcha").doc(documentId);

        //     // ドキュメントを削除
        //     docRef.delete()
        //         .then(() => {
        //             console.log("ドキュメントが削除されました");
        //         })
        //         .catch((error) => {
        //             console.error("ドキュメントの削除中にエラーが発生しました", error);
        //         });
        // }

        $(document).on('click', '.delete-btn', function() {
            const documentId = $(this).data('id');
            deleteDoc(doc(db, "tvcha", documentId));
            // 削除操作の呼び出し
            // deleteDoc(documentId);
            console.log(documentId);
        });
    </script>

    <script>
        // ⭐️３番目の挙動 => jsonからスタンプリストを作成
        // const stampList = <?= $json ?>;
        // console.log(stampList);

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
        // const chart = <?= $jsonChart ?>;
        // console.log(chart);


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
    </script>




</body>

</html>