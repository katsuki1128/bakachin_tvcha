import { initializeApp } from 'firebase/app';
import { Text, View, Image, StyleSheet } from "react-native";
import React, { useState, useEffect } from "react";
import "firebase/firestore";
import "firebase/storage";
import firebase from "firebase/app";



// Firebaseの設定情報
if(!firebase.getApps.length){
const firebaseConfig = {
  apiKey: "AIzaSyBs-rcINsUSZe7bD7OeLTrNcXm6-OInABg",
  authDomain: "tvcha-9cae7.firebaseapp.com",
  databaseURL: 'https://tvcha-9cae7.firebaseio.com',
  projectId: "tvcha-9cae7",
  storageBucket: "tvcha-9cae7.appspot.com",
  messagingSenderId: "866848033597",
  appId: "1:866848033597:web:c6887382eb14ee58351354",
};

// Firebaseの初期化
// if (!firebase.apps.length) {
  // firebase.initializeApp(firebaseConfig);
// }

const app = initializeApp(firebaseConfig);
}

export const MyComponent = () => {
  const [documentData, setDocumentData] = useState([]);

  useEffect(() => {
    getFirebaeItems();
  }, []);

  const getFirebaeItems = async () => {
    const documentRef = await firebase.firestore().collection("tvcha").get();
    const tvChat = documentRef.docs.map((doc) => doc.data());
    console.log(tvChat);
    setDocumentData(tvChat);
  };

  const tvChatItems = documentData.map((data, index) => (
    <View key={index}>
      <Image style={styles.image} source={{ uri: data.img }} />
      <Text>ポイント: {data.point}</Text>
      <Text>時刻: {data.time}</Text>
    </View>
  ));

  return <View style={styles.container}>{tvChatItems}</View>;
};



const styles = StyleSheet.create({
  container: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
  },
  image: {
    width: 100,
    height: 100,
  },
});