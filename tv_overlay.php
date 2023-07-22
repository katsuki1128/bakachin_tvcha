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

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

</head>

<body>
    <!-- ⭐️スタンプ表示エリア -->

    <div>
        <canvas id="overlay" width="640" height="360"></canvas>
    </div>


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

        // データ取得処理(データベース上でデータの変更が発生したタイミングで {} 内の処理を実行)
        onSnapshot(q, (querySnapshot) => {
            const documents = [];
            querySnapshot.docs.forEach(function(doc) { //docsは配列 docはオブジェクト
                const document = {
                    id: doc.id, //ドキュメントID
                    data: doc.data(), //ドキュメントの配列データ
                };
                documents.push(document); //配列に格納
            });

            // ドキュメントを取得して配列に格納
            const dataArray = documents.map((document, index) => ({
                id: index,
                ...document.data
            }));

            //----------------------------------------
            // ▼チャートの描画
            //----------------------------------------

            // データの収集
            const countData = dataArray.map(item => item.count);

            // 画像のURLを収集
            const imageUrls = dataArray.map(item => item.img);

            // 画像の読み込みが完了したら描画を開始する
            const loadImage = (url) => {
                return new Promise((resolve, reject) => {
                    const img = new Image();
                    img.src = url;
                    img.onload = () => resolve(img);
                    img.onerror = (error) => reject(error);
                });
            };

            // チャートの描画
            const canvas = document.getElementById('overlay');
            const ctx = canvas.getContext('2d');

            // 画像をcanvasに描画する関数
            const drawImageOnCanvas = async (url, x, y) => {
                try {
                    const img = await loadImage(url);
                    ctx.drawImage(img, x, y);
                } catch (error) {
                    console.error('画像の読み込みエラー:', error);
                }
            };

            // 画像をコンテナに追加し、アニメーションを開始
            const imageContainer = document.querySelector('.image-container');
            imageUrls.forEach((url, index) => {
                drawImageOnCanvas(url, index * 100, 10); // x座標とy座標を適切な位置に設定
            });

        });
    </script>
</body>

</html>