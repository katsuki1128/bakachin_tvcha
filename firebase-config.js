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
        
export { app, storage, db, q ,onSnapshot};