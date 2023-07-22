<?php

?>

<!DOCTYPE html>
<html lang="ja">


<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>スタンプ作成画面</title>

    <!-- CSS & Tailwind -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="reset.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/flowbite@1.4.4/dist/flowbite.min.css" />

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/chart.piecelabel.js/0.15.0/Chart.PieceLabel.min.js"></script> -->
    <!-- <script src="https://cdn.jsdelivr.net/gh/emn178/chartjs-plugin-labels/src/chartjs-plugin-labels.js"></script> -->

    <!-- fontAwesome -->
    <script src="https://kit.fontawesome.com/e6a146d4cb.js" crossorigin="anonymous"></script>


    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.7.0/flowbite.min.css" rel="stylesheet" /> -->

    <!-- <script src="tvcha-app/node_modules/chartjs-plugin-labels/dist/chartjs-plugin-labels.js"></script> -->

</head>

<body>

    <!----------------------------------------------
    ⭐️ここが大元！スタンプ生成ブロック  
    ------------------------------------------------->

    <section class="bg-gray-200">
        <div class="flex flex-col items-center justify-center px-6 pt-8 pb-4 mx-auto">
            <div class="w-full bg-white rounded-lg shadow sm:max-w-3xl md:w-4/5 xl:p-0">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">

                    <div>
                        <img class="w-40 h-15 mr-2" src="./img/tvcha_logo.png" alt="logo">
                    </div>
                    <div>
                        <h1 class="text-xl font-bold mb-3 leading-tight tracking-tight text-purple-800 md:text-2xl">
                            スタンプを登録する
                        </h1>
                    </div>
                    <form>
                        <div class="flex items-start">
                            <div class="w-3/4">

                                <!-- 入力エリア -->

                                <!-- 画像登録 -->
                                <div class="w-full flex items-start mt-3 mb-3">
                                    <label class="w-1/4 block mt-3 mb-3 text-sm font-medium text-gray-900" for="id">画像登録</label>
                                    <label class="block">
                                        <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="img" type="file">
                                    </label>
                                </div>


                                <!-- ポイント登録 -->
                                <div class="flex items-start mt-3 mb-3 ">
                                    <label for="point" class="w-1/4 mt-3 mb-3 text-sm font-medium text-gray-900">ポイント</label>
                                    <input type="text" name="point" id="point" placeholder="000" class="w-3/4 bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block p-2.5" required="">
                                </div>
                                <div><input type="hidden" id="count" value=0 /></div>
                            </div>

                            <!-- 入力エリア -->
                            <div>
                                <div id="imagePreview"></div>
                            </div>
                        </div>

                        <!-- <button type="button" id="send" class="w-full mt-3 mb-3 text-white bg-purple-600 hover:bg-purple-700 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">登録</button> -->
                        <button type="button" id="send" class="w-full mt-3 mb-3 text-white bg-purple-600 hover:bg-purple-700 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" disabled>登録</button>

                    </form>

                </div>
            </div>
        </div>
    </section>

    <!-- ⭐️スタンプ一覧表示エリア -->
    <section class="bg-gray-200">
        <div class="flex flex-col items-center justify-center px-6 pt-4 pb-4 mx-auto">
            <div class="w-full bg-white rounded-lg shadow sm:max-w-3xl md:w-4/5 xl:p-0">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <h1 class="text-xl font-bold mb-3 leading-tight tracking-tight text-purple-800 md:text-2xl">
                        スタンプ一覧
                    </h1>
                    <!-- ⭐️スタンプ表示エリア -->
                    <div class="flex flex-col items-center justify-center">
                        <p class="w-full" id="output"></p>
                    </div>

                    <!-- ⭐️円グラフ表示エリア -->
                    <div class="w-full flex flex-col items-center justify-center" id="chart_wrapper">
                        <canvas id="myChart"></canvas>
                    </div>

                </div>
            </div>
        </div>
    </section>

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
        // ▼画像とポイント入力フォームに半角数字が入力された時のみ登録ボタンが有効になる
        //----------------------------------------

        $(document).ready(function() {
            function toggleButton() {
                const imgFile = $("#img")[0].files[0]; // 選択された画像ファイルを取得
                const pointValue = $("#point").val(); // ポイントの入力値を取得

                // #pointに全角文字が入力されている場合はボタンを無効化
                if (hasFullWidthCharacter(pointValue) || !hasHalfWidthNumber(pointValue)) {
                    $("#send").prop("disabled", true);
                } else {
                    // 画像が選択されている場合はボタンを有効化
                    if (imgFile) {
                        $("#send").prop("disabled", false);
                    } else {
                        $("#send").prop("disabled", true);
                    }
                }
            }

            // 画像ファイルが変更されたらボタンの有効/無効を切り替える
            $("#img").on("change", toggleButton);

            // ポイントの入力が変更されたらボタンの有効/無効を切り替える
            $("#point").on("input", toggleButton);

            // 全角文字が含まれるかをチェックする関数
            function hasFullWidthCharacter(str) {
                for (let i = 0; i < str.length; i++) {
                    //charCodeAtの0xFF01は全角の！の文字コード、0xFF5Eは全角の～の文字コード
                    if (str.charCodeAt(i) >= 0xFF01 && str.charCodeAt(i) <= 0xFF5E) {
                        return true;
                    }
                }
                return false;
            }

            // 半角数字のみが含まれるかをチェックする関数
            function hasHalfWidthNumber(str) {
                //.testは正規表現にマッチするかどうかを調べるメソッド /^[0-9]+$/は半角数字のみを表す正規表現 +$は1文字以上の繰り返し
                return /^[0-9]+$/.test(str);
            }


            //----------------------------------------
            // ▼登録ボタンクリック時にデータを送信する処理を実装
            //----------------------------------------

            // 登録ボタンがクリックされたときの処理
            $("#send").on("click", function() {
                // ボタンの有効/無効を判定する処理は、上記のtoggleButton()関数で行っているため、ここではそのまま送信処理を行う
                const imgFile = $("#img")[0].files[0]; // 選択された画像ファイルを取得
                const pointValue = $("#point").val().trim();

                // 以下の処理は画像が選択され、かつ#pointに全角文字が含まれていない場合のみ実行される
                const storageRef = ref(storage, 'images/' + imgFile.name);
                uploadBytes(storageRef, imgFile)
                    .then((snapshot) => {
                        return getDownloadURL(snapshot.ref);
                    })
                    .then((downloadURL) => {
                        console.log('ダウンロード URL:', downloadURL);

                        const postData = {
                            img: downloadURL,
                            point: Number($("#point").val()),
                            count: Number($("#count").val()),
                            time: serverTimestamp(),
                        };

                        addDoc(collection(db, "tvcha"), postData)
                            .then(() => {
                                console.log('データを Firestore に保存しました');
                                $("#img,#point,#count").val("");
                                $("#send").prop("disabled", true); // 送信後、ボタンを無効化
                            })
                            .catch((error) => {
                                console.error('データの保存中にエラーが発生しました', error);
                            });
                    })
                    .catch((error) => {
                        console.error('画像のアップロード中にエラーが発生しました', error);
                    });
            });

            // 初期状態でボタンを無効化
            $("#send").prop("disabled", true);
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

            const countData = []; // countDataを初期化しておく
            const imageUrls = []; // imageUrlsを初期化しておく

            //----------------------------------------
            // id: 'ドキュメントのID',
            // data: {
            //      img: '画像のURL',
            //      point: 'ポイント',
            //      count: 'カウント',
            //      time: '作成日時など'
            //----------------------------------------

            let tableRows = '';
            documents.forEach(function(document, index) {
                const deleteButton = `<button class="delete-btn" data-id="${document.id}"><i class="fas fa-trash"></i></button>`;
                const rowStyleClass = index % 2 === 0 ? 'even-row' : 'odd-row'; // 奇数行と偶数行でスタイルを切り替える

                // countDataにdocument.data.countを追加
                countData.push(document.data.count);

                // imageUrlsにdocument.data.imgを追加
                imageUrls.push(document.data.img);

                console.log("countData", countData, "imageUrls", imageUrls);

                tableRows += `
                    <tr style="height: 46px;" class="${rowStyleClass}">
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
                <table class="w-full">
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

                inputElement.focus().keydown(function(event) {
                    if (event.which === 13) { // エンターキーのキーコードは13
                        event.preventDefault(); // デフォルトのイベント（改行）をキャンセル
                        let inputVal = $(this).val();

                        // 全角数字を半角数字に変換
                        inputVal = inputVal.replace(/[０-９]/g, function(s) {
                            return String.fromCharCode(s.charCodeAt(0) - 0xFEE0);
                        });

                        // エラーチェック：全角文字が含まれているかを検証
                        if (/^[^\x01-\x7E\xA1-\xDF]+$/.test(inputVal)) {
                            alert("入力に全角文字が含まれています。半角文字のみ入力してください。");
                            return;
                        }
                        // Firebaseのデータを更新する処理を追加
                        const documentId = $(this).closest('tr').find('.delete-btn').data('id');
                        updateFirebaseData(documentId, inputVal);

                        $(this).parent().removeClass('on').text(inputVal);
                    }

                }).blur(function() {
                    let inputVal = $(this).val();

                    // エラーチェック：全角文字が含まれているかを検証
                    if (/^[^\x01-\x7E\xA1-\xDF]+$/.test(inputVal)) {
                        return;
                    }

                    // Firebaseのデータを更新する処理を追加
                    const documentId = $(this).closest('tr').find('.delete-btn').data('id');
                    updateFirebaseData(documentId, inputVal);

                    $(this).parent().removeClass('on').text(inputVal);
                });

                // テキスト全体を選択状態にする
                inputElement.select();
            });


            //----------------------------------------
            // ▼チャートの描画
            //----------------------------------------

            // 色の設定
            const colors = ['#7e7d9d', '#9d97b1', '#e1e1f0', '#bfb8c6', '#9e8f9e', '#1e1e33'].slice(0, documents.length);

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