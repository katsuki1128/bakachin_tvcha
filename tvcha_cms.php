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
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

</head>

<body>


    <!----------------------------------------------
    ⭐️ここが大元！スタンプ生成ブロック  
    ------------------------------------------------->

    <form>
        <fieldset>
            <legend>スタンプ登録画面</legend>
            <div>画像登録： <input type="file" id="img" /></div>
            <!-- <div>スタンプ名： <input type="text" id="name" /></div> -->
            <div>ポイント： <input type="text" id="point" /></div>
            <div><input type="hidden" id="count" value=0 /></div>
            <div>
                <button type="button" id="send">登録</button>
            </div>
        </fieldset>
    </form>

    <p id="output"></p>

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
            onSnapshot, // Firestore 上に保存されているデータを取得して console に出力
            doc,
            deleteDoc,
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
                        <td><div class="image_thumnail" id="image-${index}"></div></td>
                        <td>${document.data.point}</td>
                        <td>${convertTimestampToDatetime(document.data.time.seconds)}</td>
                        <td>${document.data.count}</td>
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
                        <th>作成日時</th>
                        <th>クリック数</th>
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

        $(document).on('click', '.delete-btn', function() {
            const documentId = $(this).data('id');
            deleteDoc(doc(db, "tvcha", documentId));
            // 削除操作の呼び出し
            // deleteDoc(documentId);
            console.log(documentId);
        });

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
            return `${Y}/${m}/${d} ${H}:${i}:${s}`;
        }
    </script>

</body>

</html>

<!-- <td>${idFormatted}</td> -->
<!-- <th>ID</th> -->