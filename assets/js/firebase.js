var config = {
  apiKey: "AIzaSyC7mv5UM1hMldKdMfesKlK5wwKdu3BXIMs",
  authDomain: "ptoventa-3b5ed.firebaseapp.com",
  databaseURL: "https://ptoventa-3b5ed.firebaseio.com",
  projectId: "ptoventa-3b5ed",
  storageBucket: "ptoventa-3b5ed.appspot.com",
  messagingSenderId: "901936782630",
  appId: "1:901936782630:web:9e2cfdc8cac46a4e9735e7"
};
var token = "";
firebase.initializeApp(config);
//Firebase Messaging
const messaging = firebase.messaging();
//Firabase Database
var db = firebase.database(); 

navigator.serviceWorker.register('firebase-messaging-sw.js')
.then(function (registration) {
    messaging.useServiceWorker(registration);
        
    // Request for permission
    messaging.requestPermission()
    .then(function() {
      console.log('Notification permission granted.');
      // TODO(developer): Retrieve an Instance ID token for use with FCM.
      messaging.getToken()
      .then(function(currentToken) {
        if (currentToken) {
          token = currentToken;
          console.log('Token: ' + currentToken)
          sendTokenToServer(currentToken);
        } else {
          console.log('No Instance ID token available. Request permission to generate one.');
          setTokenSentToServer(false);
        }
      })
      .catch(function(err) {
        console.log('An error occurred while retrieving token. ', err);
        setTokenSentToServer(false);
      });
    })
    .catch(function(err) {
      console.log('Unable to get permission to notify.', err);
    });
});

// Handle incoming messages
messaging.onMessage(function(payload) {
  console.log("Notification received: ", payload);
  notify(payload.data.message, "success", 2);
});

// Callback fired if Instance ID token is updated.
messaging.onTokenRefresh(function() {
  messaging.getToken()
  .then(function(refreshedToken) {
    
    console.log('Token refreshed.');
    // Indicate that the new Instance ID token has not yet been sent 
    // to the app server.
    setTokenSentToServer(false);
    // Send Instance ID token to app server.
    sendTokenToServer(refreshedToken);
  })
  .catch(function(err) {
    console.log('Unable to retrieve refreshed token ', err);
  });
});

// Send the Instance ID token your application server, so that it can:
// - send messages back to this app
// - subscribe/unsubscribe the token from topics
function sendTokenToServer(currentToken) {
  if (!isTokenSentToServer()) {
    console.log('Sending token to server...');
    // TODO(developer): Send the current token to your server.
    setTokenSentToServer(true);
  } else {
    console.log('Token already sent to server so won\'t send it again ' +
        'unless it changes');
    subscribeTokenToTopic('general');
  }
}

function isTokenSentToServer() {
  return window.localStorage.getItem('sentToServer') == 1;
}

function setTokenSentToServer(sent) {
  window.localStorage.setItem('sentToServer', sent ? 1 : 0);
}

function subscribeTokenToTopic(topic) {
  console.log("Suscribir Token to "+topic);

  if(token == ""){
    console.log("No hay token de Firebase");
    return;
  }

  var parametros = {
    token: token,
    topic: topic
  }
  $.ajax({
    url: 'controllers/controlador_usuario.php?metodo=subscribeTokenToTopic',
    data: parametros,
    type:'POST',
    success: function(resp){
      console.log("Suscrito al topic");
    },
    error: function(resp){
      console.log(resp);
    }
  });
}

function unSubscribeTokenToTopic(topic) {
  console.log("Desuscribir Token to "+topic);

  if(token == ""){
    console.log("No hay token de Firebase");
    return;
  }

  var parametros = {
    token: token,
    topic: topic
  }
  $.ajax({
    url: 'controllers/controlador_usuario.php?metodo=unSubscribeTokenToTopic',
    data: parametros,
    type:'POST',
    success: function(resp){
      console.log("Des Suscrito al topic");
    },
    error: function(resp){
      console.log(resp);
    }
  });
}


/*FUNCTIONS DATABASE*/
function sendMessage(mensaje){
  console.log("CHAT MENSAJE: "+mensaje);
  var fecha = new Date().toDateString();
  db.ref('chat/mensajes').push({
    fecha: fecha,
    id_persona: 1,
    mensaje: mensaje
  });
}

function getDatabase(){
  return db;
}

function createChat(){
  db.ref('chat').push({
    personas: [{
      id:1,
      nombre:"Sebastian Villanueva"
    },{
      id:2,
      nombre:"Juan Bastidas"
    }]
  });
  console.log("CHAT CREADO");
}

// Get the data on a post that has changed
db.ref('chat').on("child_changed", function(data) {
  var changedPost = data.val();
  console.log(changedPost);
  //console.log("The updated post title is " + changedPost.title);
});

function setLoginFirebase(token,data){
  db.ref('auth/'+token).set(data);
  localStorage.setItem('token', token);
}

function deleteLoginFirebase(token){
  db.ref('auth/'+token).remove();
}

if(localStorage.getItem('token') !== null){
  let token = localStorage.getItem('token');
  console.log('Esperando cambios en auth/'+token);
  db.ref('auth/'+token).on("child_removed", function(data) {
    window.location.href = "login.php";
  });
}

//child_removed 

