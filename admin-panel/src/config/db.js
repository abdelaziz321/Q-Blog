import firebase from 'firebase/app';
import 'firebase/firestore';

let app = firebase.initializeApp({
  apiKey: "",
  authDomain: "",
  databaseURL: "",
  projectId: "",
  storageBucket: "",
  messagingSenderId: ""
});

export const db = app.firestore();