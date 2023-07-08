<?php


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
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <!-- <script src="https://cdn.jsdelivr.net/gh/emn178/chartjs-plugin-labels/src/chartjs-plugin-labels.js"></script> -->


    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.7.0/flowbite.min.css" rel="stylesheet" /> -->

</head>

<body>

    <!----------------------------------------------
    ⭐️ここが大元！スタンプ生成ブロック  
    ------------------------------------------------->

    <form>
        <fieldset>
            <legend>スタンプ⭐️登録画面</legend>
            <div>画像登録：
                <input type="file" id="img" />
            </div>

            <div>ポイント： <input type="text" id="point" /></div>
            <div><input type="hidden" id="count" value=0 /></div>
            <div>
                <button type="button" id="send">登録</button>
            </div>
            <div id="imagePreview"></div>
        </fieldset>
    </form>

    <!-- ⭐️スタンプ一覧表示エリア -->
    <fieldset>
        <legend>スタンプ⭐️一覧</legend>
        <p id="output"></p>
    </fieldset>


    <!-- ⭐️円グラフ表示エリア -->

    <div id="chart_wrapper">
        <canvas id="myChart"></canvas>
    </div>

    <!----------------------------------------------
    ⭐️選択した画像のプレビュー表示
    ------------------------------------------------->
    <script>
        $("#img").on("change", function() {
            const file = this.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                $("#imagePreview").html(`<img src="${e.target.result}" />`);
            };
            reader.readAsDataURL(file);
        });

        $("#send").click(function() {
            $("#imagePreview").empty();
        });
    </script>

    <!----------------------------------------------
    ⭐️tailwind test 
    ------------------------------------------------->

    <script type="module">
        //----------------------------------------
        // ▼firebaseプロジェクトとjavaScriptを連携させる
        //----------------------------------------

        // 必要な機能をSDKからインポート
        import {
            initializeApp
        } from "https://www.gstatic.com/firebasejs/9.22.2/firebase-app.js";


        // firebase firestoreとやり取りをする設定
        import {
            getFirestore,
            collection,
            addDoc,
            serverTimestamp,
            query,
            orderBy, //データのソート
            onSnapshot, // Firestore 上に保存されているデータを取得
            doc,
            deleteDoc,
            updateDoc,
        } from "https://www.gstatic.com/firebasejs/9.22.2/firebase-firestore.js";

        // firebase storageとやり取りをする設定
        import {
            getStorage,
            ref,
            uploadBytes,
            getDownloadURL
        } from "https://www.gstatic.com/firebasejs/9.22.2/firebase-storage.js";

        // ウェブアプリのFirebaseの設定
        const firebaseConfig = {
            apiKey: "AIzaSyBs-rcINsUSZe7bD7OeLTrNcXm6-OInABg",
            authDomain: "tvcha-9cae7.firebaseapp.com",
            projectId: "tvcha-9cae7",
            storageBucket: "tvcha-9cae7.appspot.com",
            messagingSenderId: "866848033597",
            appId: "1:866848033597:web:c6887382eb14ee58351354",
        };

        // Firebaseの初期化
        const app = initializeApp(firebaseConfig);

        // FirebaseアプリとCloud Storageの連携を初期化しセットアップする
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
                        point: Number($("#point").val()), // 文字列を数値に変換
                        count: Number($("#count").val()), // 文字列を数値に変換
                        time: serverTimestamp(),
                    };

                    addDoc(collection(db, "tvcha"), postData)
                        .then(() => {
                            console.log('データを Firestore に保存しました');
                            $("#img,#point,#count").val(""); // フォームをリセット
                        })
                        .catch((error) => {
                            console.error('データの保存中にエラーが発生しました', error);
                        });
                })
                .catch((error) => {
                    console.error('画像のアップロード中にエラーが発生しました', error);
                });
        });


        //----------------------------------------
        // ▼変更ボタン関数
        //----------------------------------------

        function updateFirebaseData(documentId, newPoint) {
            // Firestoreのドキュメント参照を作成
            const docRef = doc(db, "tvcha", documentId);

            // データを更新する
            updateDoc(docRef, {
                    point: newPoint
                })
                .then(() => {
                    console.log('Firebaseのデータを更新しました');
                })
                .catch((error) => {
                    console.error('Firebaseのデータの更新中にエラーが発生しました', error);
                });
        }


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
                // const idFormatted = String(dataArray[index].id).padStart(3, '0');
                const deleteButton = `<button class="delete-btn" data-id="${document.id}">削除</button>`;
                // const editButton = `<button class="edit-btn" data-id="${document.id}">変更</button>`;

                tableRows += `
                    <tr style="height: 46px;">
                        <td>
                            <div class="image_thumnail" id="image-${index}"></div>
                        </td>
                        <td class=point_area>
                            ${document.data.point}
                        </td>
                        <td>${document.data.count}</td>
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
                        <th>スタンプ</th>
                        <th>ポイント</th>
                        <th>クリック数</th>
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


            //----------------------------------------
            // ▼画像の更新関数
            //----------------------------------------

            $('.image_thumnail').click(function() {
                const index = $(this).attr('id').split('-')[1]; // 画像のインデックスを取得
                console.log(index);

                // <input type="file"> 要素を作成
                const inputFile = $('<input type="file">');

                // クリックされた画像のドキュメントIDを取得
                const documentId = $(this).closest('tr').find('.delete-btn').data('id');
                console.log('クリックされた画像のドキュメントID:', documentId);

                // ファイル選択時のイベントハンドラを設定
                inputFile.change(function() {
                    const file = this.files[0]; // 選択されたファイルを取得

                    // 画像を表示
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const imgElement = $('<img>').attr('src', e.target.result);
                        $(`#image-${index}`).html(imgElement);
                    };
                    reader.readAsDataURL(file);

                    // 画像を Firebase Storage にアップロード
                    const storageRef = ref(storage, 'images/' + file.name);
                    uploadBytes(storageRef, file)
                        .then((snapshot) => {
                            return getDownloadURL(snapshot.ref);
                        })
                        .then((downloadURL) => {


                            // Firebase Firestoreの該当データの画像URLを更新する
                            const docRef = doc(db, 'tvcha', documentId);
                            console.log('ダウンロード⭐️URL:', downloadURL, docRef);
                            updateDoc(docRef, {
                                    img: downloadURL // ダウンロードURLを指定してフィールドを更新
                                })
                                .then(() => {
                                    console.log('Firebase Realtime Databaseの画像URLを更新しました');
                                })
                                .catch((error) => {
                                    console.error('Firebase Realtime Databaseの画像URLの更新中にエラーが発生しました', error);
                                });
                        })
                        .catch((error) => {
                            console.error('画像のアップロード中にエラーが発生しました', error);
                        });
                });

                // ファイル選択ダイアログを表示
                inputFile.click();
            });
            //----------------------------------------
            // ▼その場編集の実装
            //----------------------------------------

            $('.point_area').click(function() {
                $(this).addClass('on');
                let txt = $(this).text().trim(); // テキストをトリムする

                console.log("クリックされてます", txt);

                const inputElement = $('<input type="text">').val(txt); // <input> 要素を作成し、値を設定
                inputElement.addClass('input-point'); // クラスを追加

                $(this).empty().append(inputElement); // 要素を一度空にしてから <input> 要素を追加

                inputElement.focus().blur(function() {
                    let inputVal = $(this).val();
                    if (inputVal === '') {
                        inputVal = this.defaultValue;
                    }
                    // // Firebaseのデータを更新する処理を追加
                    const documentId = $(this).closest('tr').find('.delete-btn').data('id');
                    updateFirebaseData(documentId, inputVal);

                    $(this).parent().removeClass('on').text(inputVal);
                });
            });


            // データの収集
            const countData = dataArray.map(item => item.count);

            // 画像のURLを収集
            const imageUrls = dataArray.map(item => item.img);

            console.log(countData, imageUrls);

            // 色の設定
            const colors = ['#687c8d', '#96abbd', '#e9e9e9', '#c5bfb9', '#948f89', '#000000'].slice(0, dataArray.length);

            // チャートを作成する前に既存のチャートを破棄
            const existingChart = Chart.getChart('myChart');
            if (existingChart) {
                existingChart.destroy();
            }

            // チャートの描画
            const canvas = document.getElementById('myChart');
            const ctx = canvas.getContext('2d');

            function drawImageLabel(context) {

            }

            const myChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: countData,
                        backgroundColor: colors
                    }],
                },
                plugins: [
                    ChartDataLabels
                ],
                options: {
                    plugins: {
                        datalabels: {
                            color: '#000',
                            font: {
                                size: 18
                            },
                            display: true,

                        },
                        legend: {}
                    },
                },

            });
        });

        $(document).on('click', '.delete-btn', function() {
            const documentId = $(this).data('id');
            deleteDoc(doc(db, "tvcha", documentId));

            // 削除操作の呼び出し
            console.log(documentId);
        });


        // チャートの作成処理

        //----------------------------------------
        // ▼時刻変換関数
        //----------------------------------------

        function convertTimestampToDatetime(timestamp) {
            const _d = timestamp ? new Date(timestamp * 1000) : new Date();
            const Y = _d.getFullYear();
            const m = (_d.getMonth() + 1).toString().padStart(2, "0");
            const d = _d.getDate().toString().padStart(2, "0");
            const H = _d.getHours().toString().padStart(2, "0");
            const i = _d.getMinutes().toString().padStart(2, "0");
            const s = _d.getSeconds().toString().padStart(2, "0");
            return `${m}/${d} ${H}:${i}`;
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.7.0/flowbite.min.js"></script>
</body>

</html>

<!-- <td>${idFormatted}</td> -->
<!-- <th>ID</th> -->