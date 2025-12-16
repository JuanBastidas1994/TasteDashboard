// Give the service worker access to Firebase Messaging.
	importScripts('https://www.gstatic.com/firebasejs/4.9.1/firebase-app.js')
	importScripts('https://www.gstatic.com/firebasejs/4.9.1/firebase-messaging.js')

// Your web apps Firebase configuration
	  var firebaseConfig = {
	    apiKey: "AIzaSyC7mv5UM1hMldKdMfesKlK5wwKdu3BXIMs",
	    authDomain: "ptoventa-3b5ed.firebaseapp.com",
	    databaseURL: "https://ptoventa-3b5ed.firebaseio.com",
	    projectId: "ptoventa-3b5ed",
	    storageBucket: "ptoventa-3b5ed.appspot.com",
	    messagingSenderId: "901936782630",
	    appId: "1:901936782630:web:9e2cfdc8cac46a4e9735e7"
	  };
	  // Initialize Firebase
	  firebase.initializeApp(firebaseConfig);
	  const messaging = firebase.messaging();


	  messaging.setBackgroundMessageHandler(function(payload) {
	  console.log('[firebase-messaging-sw.js] Received background message ', payload);
	  // Customize notification here

	  const notificationTitle = payload.data.title;
	  const notificationOptions = {
	    body: payload.data.message,
	    icon: './firebase-logo.png'
	  };


	  return self.registration.showNotification(notificationTitle,
	    notificationOptions);
	});