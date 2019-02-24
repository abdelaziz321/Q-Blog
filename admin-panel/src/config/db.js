import firebase from 'firebase/app';
import 'firebase/firestore';

let app = firebase.initializeApp({
  apiKey: "AIzaSyAZVSFu8N4oiHKWTzy8i7T2Sz7kFUeF_Zk",
  authDomain: "test-q-blog.firebaseapp.com",
  databaseURL: "https://test-q-blog.firebaseio.com",
  projectId: "test-q-blog",
  storageBucket: "test-q-blog.appspot.com",
  messagingSenderId: "1077513401582"
});

export const db = app.firestore();