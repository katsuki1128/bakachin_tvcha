import {
  StyleSheet,
  Text,
  View,
  StatusBar,
  Image,
  Button,
  TouchableOpacity,
} from 'react-native';

import React, { useState, useEffect } from 'react';

import "firebase/firestore";
import { initializeApp } from "firebase/app";

import { Camera, CameraType } from 'expo-camera';

import {
  getFirestore,
  collection,
  getDoc,
  doc,
  updateDoc,
  onSnapshot,
} from "firebase/firestore";
  
import { getStorage, ref } from "firebase/storage";
// import * as React from 'react';
// import {NavigationContainer} from '@react-navigation/native';

const firebaseConfig = {
  apiKey: "AIzaSyBs-rcINsUSZe7bD7OeLTrNcXm6-OInABg",
  authDomain: "tvcha-9cae7.firebaseapp.com",
  projectId: "tvcha-9cae7",
  storageBucket: "tvcha-9cae7.appspot.com",
  messagingSenderId: "866848033597",
  appId: "1:866848033597:web:c6887382eb14ee58351354"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const db = getFirestore(app);

// ストレージにアクセスするための接続情報を取得
const storage = getStorage(app);
// ストレージサービスからストレージ参照を作成する
const storageRef = ref(storage);

//----------------------------------------
// ▼firebaseから読み込んで画面に表示する
//----------------------------------------

// ⭐️stampListという空の配列を作り、今後setStampListメソッドで
// 更新していくことをuseState([])で定義
const App = () => {
  const [stampList, setStampList] = useState([]);
  const [userList, setUserList] = useState([]);

// ⭐️初回読み込み時のみスタンプリストを読み込む
useEffect(() => {
  onSnapshot(collection(db, "tvcha"), (querySnapshot) => {
    const newStampList = querySnapshot.docs.map((doc) => {
      const { img, point, count } = doc.data();
      return {
        id: doc.id,
        img,
        point,
        count,
      };
    });
    setStampList(newStampList);
  });
}, []);

// ⭐️初回読み込み時のみユーザー情報を読み込む
useEffect(() => {
  onSnapshot(collection(db, "tvcha-user"), (querySnapshot) => {
    const newUserList = querySnapshot.docs.map((doc) => {
      const { user, point } = doc.data();
      return {
        id: doc.id,
        user,
        point,
      };
    });
    setUserList(newUserList);
  });
}, []);
    
  return (  
    <View style={[styles.container]}>
      <Text style={{ fontSize: 36 }}>👇テレビをスマホに配信</Text>
      <Image
        style={styles.imagetv}
        source={require('./Sequence04.gif')} />

      
      <Text style={{ fontSize: 36 }}>👇スタンプエリア</Text>
      <View style={styles.row}>
        {stampList.map((data, id) => (
          <TouchableOpacity
          key={id}
          style={styles.stampContainer}i
          onPress={() => handleItemClick(data, "tvcha", data.id, "tvcha-user", userList[0].id)}
          >
          <Image
            style={styles.image}
            source={{ uri: data.img }} />
          <Text
            style={[styles.text, { textAlign: 'center' }]}>
             {data.point}
            </Text>
          {/* <Text
          style={styles.text}>
            {data.count}
        </Text> */}
        </TouchableOpacity>
        ))}
      </View>

      <Text style={{ fontSize: 36 }}>👇持ちポイント</Text>
      <View>
      
        <Text style={{ fontSize: 48 }}>
          {userList.length > 0 ? userList[0].point : ''}
        </Text>
         </View>
      <StatusBar style="auto" />
    </View>
  );
};

//----------------------------------------
// ▼firebaseに押された数を保存する
//----------------------------------------

const handleItemClick = async (data, collection1, docId1, collection2, docId2) => {
  try {
    const currentCount = data.count;
    const consumptionPoint = data.point;

    const updatedCount = currentCount + 1;

    // Firestoreのドキュメントを更新
    await updateDoc(doc(db, collection1, docId1), {
      count: updatedCount
    });

    // Firestoreからcollection2とdocId2で指定されるドキュメントを取得
    const docSnapshot = await getDoc(doc(db, collection2, docId2));
    if (docSnapshot.exists()) {
      const { point } = docSnapshot.data();

      // ドキュメントのpointを使用して更新
      await updateDoc(doc(db, collection2, docId2), {
        point: point - consumptionPoint
      });

      console.log("カウントが正常に更新されました。");
    } else {
      console.log("指定されたドキュメントが存在しません。");
    }
  } catch (error) {
    console.error("カウントの更新中にエラーが発生しました:", error);
  }
};

//----------------------------------------
// ▼カメラを起動する
//----------------------------------------
// export default function App() {
//   const [type, setType] = useState(CameraType.back);
//   const [permission, requestPermission] = Camera.useCameraPermissions();

//   function toggleCameraType() {
//     setType(current => (current === CameraType.back ? CameraType.front : CameraType.back));
//   }

//   return (
//     <View style={styles.container}>
//       <Camera style={styles.camera} type={type}>
//         <View style={styles.buttonContainer}>
//           <TouchableOpacity style={styles.button} onPress={toggleCameraType}>
//             <Text style={styles.text}>Flip Camera</Text>
//           </TouchableOpacity>
//         </View>
//       </Camera>
//     </View>
//   );
// }



//----------------------------------------
// ▼styleを設定する
//----------------------------------------


const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: 'white',
    alignItems: 'center',
    justifyContent: 'center',
  },
  text: {
    fontSize: 18,
  },
  imagetv: {
    width: 480,
    height: 240,
  },
  image: {
    width: 80,
    height: 80,
  },
  row: {
    flexDirection: 'row',
    flexWrap: 'wrap',
  },
  stampContainer: {
    margin: 10,    
  },
});

export default App;

