importScripts('https://www.gstatic.com/firebasejs/9.6.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.6.1/firebase-messaging-compat.js');

const firebaseConfig = {
    apiKey: "AIzaSyBLWxsInlBmzTrwSyYwIPeG9UCwG012mJk",
    authDomain: "react-journal-app-b59f9.firebaseapp.com",
    projectId: "react-journal-app-b59f9",
    storageBucket: "react-journal-app-b59f9.appspot.com",
    messagingSenderId: "407406159229",
    appId: "1:407406159229:web:3e1e33f8a75d7119995814",
    measurementId: "G-S9ECGZWPZX"
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

// Customize background notification handling here
messaging.onBackgroundMessage((payload) => {
  console.log('Background Message:', payload);
  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
  };
  self.registration.showNotification(notificationTitle, notificationOptions);
});