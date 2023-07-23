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
            where,
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

        // クリックされたスタンプのURLを格納する配列
        let clickStamp = "";
        const clickStamps = [];

        // データ取得処理(データベース上でデータの変更が発生したタイミングで {} 内の処理を実行)
        onSnapshot(q, (querySnapshot) => {
            const documents = [];

            querySnapshot.docs.forEach(function(doc) { //docsは配列 docはオブジェクト
                const document = {
                    id: doc.id, //ドキュメントID
                    data: doc.data(), //ドキュメントの配列データ
                };
                documents.push(document); //配列の先頭に追加 

            });

            querySnapshot.docChanges().forEach((change) => {

                clickStamp = change.doc.data().img;
                clickStamps.push(clickStamp);
                console.log("クリックされたスタンプ配列", clickStamps);

                drawImageOnCanvas(); // 画像をCanvas上に描画

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
            //      clickStamps: 'クリックされたスタンプのURL配列'
            // countData : 'カウントの配列',
            // imageUrls : '画像のURLの配列'
            //----------------------------------------

            //----------------------------------------
            // ドキュメントをループしてcountDataとimageUrlsを作成
            //----------------------------------------
            documents.forEach((document, index) => {
                const {
                    count,
                    img
                } = document.data;
                countData.push(count);
                imageUrls.push(img);
            });

            console.log("countData", countData);
            // console.log("imageUrls", imageUrls);






            // 画像の読み込みが完了したら描画を開始する
            // const loadImage = (url) => { //loadImage関数を定義 urlを引数にする
            //     return new Promise((resolve, reject) => { //Promiseを返す resolveとrejectを引数にする
            //         const img = new Image(); //new Image()をimgに代入
            //         img.src = url; //imgのsrcにurlを代入
            //         img.onload = () => resolve(img); //img.onloadが実行されたらresolve(img)を実行
            //         img.onerror = (error) => reject(error); //img.onerrorが実行されたらreject(error)を実行
            //     });
            // };

            // キャンバスの描画
            const canvas = document.getElementById('overlay');
            const ctx = canvas.getContext('2d');

            //----------------------------------------
            // ▼スタンプが放物線を描く
            //----------------------------------------

            // clickStampが更新されたら描画するための関数
            function drawImageOnCanvas() {


                const image = new Image();
                image.onload = function() {
                    let posX = 0; // 初期位置をCanvasの左端に設定
                    let posY = canvas.height; // 初期位置をCanvasの底辺に設定
                    let velocityY = -18; // 初速度を設定（下向きのため負の値）
                    const gravity = 0.5; // 重力の影響を表す定数

                    function animate() {

                        ctx.clearRect(0, 0, canvas.width, canvas.height); // Canvasをクリア

                        // 位置を更新
                        posX += 8; // 速度による位置の変化
                        velocityY += gravity; // 重力による速度の増加
                        posY += velocityY; // 速度による位置の変化

                        // スタンプがCanvas外に出たらリセット
                        // if (posY + 100 < 0) {
                        //     posY = canvas.height;
                        //     velocityY = -10; // スタンプが下に再び飛び出すための初速度
                        // }

                        // スタンプを描画
                        ctx.drawImage(image, posX, posY, 100, 100);

                        // 次のフレームの描画をリクエスト
                        requestAnimationFrame(animate);
                    }

                    // アニメーションを開始
                    animate();

                };
                image.src = clickStamps[clickStamps.length - 1]; // clickStampsに画像のURLが格納されていると仮定しています
            }




        });
    </script>
</body>

</html>