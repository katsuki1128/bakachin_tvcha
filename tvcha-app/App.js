import { StyleSheet, Text, View,StatusBar ,Image} from 'react-native';
import React, { useState, useEffect } from 'react';

import "firebase/firestore";
import { initializeApp } from "firebase/app";
import { getFirestore, collection, getDocs, } from "firebase/firestore";
import { doc, updateDoc } from "firebase/firestore";
import { getStorage, ref } from "firebase/storage";
// import * as React from 'react';
// import {NavigationContainer} from '@react-navigation/native';

import { TouchableOpacity } from 'react-native';

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


const App = () => {
  const [dataList, setDataList] = useState([]);

  useEffect(() => {
    getFirebaseItems();
  }, []);

  const getFirebaseItems = async () => {
    const querySnapshot = await getDocs(collection(db, "tvcha"));

    const newDataList = [];
    querySnapshot.forEach((doc) => {
      const { img, point,count } = doc.data();
      newDataList.push({
        id: doc.id,
        img,
        point,
        count
      });
    });
    setDataList(newDataList);
  };
    
  // コンソールログで各要素を表示
  dataList.forEach((data) => {
    // console.log(data); 
  });
  
  return (  
  <View style={[styles.container]}>
      <Text style={styles.text}>ユーザーへの表示画面</Text>
      <View style={styles.row}>
        {dataList.map((data, id) => (
          <TouchableOpacity
          key={id}
          style={styles.stampContainer}
          onPress={() => handleItemClick(data, id)}
          >
          <Image
            style={styles.image}
            source={{ uri: data.img }} />
          <Text
            style={styles.text}>
             {data.point}
          </Text>
        </TouchableOpacity>
      ))}
         </View>
      <StatusBar style="auto" />
    </View>
  );
};

//----------------------------------------
// ▼firebaseに押された数を保存する
//----------------------------------------

const handleItemClick = async (data, id) => {
  try {
    console.log(data);

      // Firestoreから該当するドキュメントを取得

      // 現在のカウント値を取得
      const currentCount = data.count;

      // カウント値をインクリメント
      const updatedCount = currentCount + 1;
      console.log(updatedCount);
    
      // Firestoreのドキュメントを更新
      await updateDoc(doc(db, "tvcha", id), {
        count: updatedCount
      });

      console.log("カウントが正常に更新されました。");
    
  } catch (error) {
    console.error("カウントの更新中にエラーが発生しました:", error);
  }
};

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
    fontSize: 16,
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
// export { db };



// import {  } from 'expo-status-bar';
// import firebase from "firebase/app";
// import { Firestore } from 'firebase/firestore';
// import { doc, getDoc } from "firebase/firestore";
// import firestore from "@react-native-firebase/firestore";
