// Give the service worker access to Firebase Messaging.
importScripts('https://www.gstatic.com/firebasejs/4.9.1/firebase-app.js')
importScripts('https://www.gstatic.com/firebasejs/4.9.1/firebase-messaging.js')

let v = "v1";
const VERSION_SERVICES_WORKER = v;
const CACHE_STATIC_NAME = "static-" + v;
const CACHE_DYNAMIC_NAME = "dynamic-" + v;
const CACHE_INMUTABLE_NAME = "inmutable-" + v; //PUEDE SERVIR PARA LIBRERIAS QUE NUNCA CAMBIEN.

const CACHE_DYNAMIC_LIMIT = 150;

const staticAssets = [
    './',
    //IMAGENES
    './images/logo.png',
    '.images/logo.jpg',
    '.images/logo-footer.png',
    '.images/logo-xs.png',
    './images/no-internet.jpg',
    '.images/icons/icon-192x192.png',
    '.images/icons/icon-512x512.png',
    //CSS
    './css/theme.css',
    './css/responsive.css',
    './css/colors.css',
    './css/custom_ul_switch.css',
    './css/custom_checkbox.css',
    './css/custom_input_group.css',
    //JS
    './config.js',
    './js/theme.js',
    './js/cart.js',
    './js/libs/jquery-3.2.1.min.js',
    './js/pages/blog.js',
    './js/pages/carrito.js',
    './js/pages/checkout.js',
    './js/pages/contactanos.js',
    './js/pages/detalle-producto.js',
    './js/pages/detalle.js',
    './js/pages/entrega.js',
    './js/pages/home.js',
    './js/pages/login.js',
    './js/pages/menu.js',
    './js/pages/mis_puntos.js',
    './js/pages/officeCoverage.js',
    './js/pages/ordenes.js',
    './js/pages/paginas.js',
    './js/pages/perfil.js',
    './js/pages/politicas.js',
    './js/pages/productos.js',
    './js/pages/promociones.js',
    './js/pages/tracking.js',
    './js/pages/update-user-information.js',
    './js/pages/user-location.js',
    //COMPONENTES
    './components/componentes.js',
    './components/footer.js',
    './components/header.js',
    './components/modal.documento.js',
    './components/modal.evento.js',
    './components/modal.fidelizacion.js',
    './components/modal.login.js',
    //HTML
    './nointernet.html'
];

const InmutableAssets = [
    './js/handlebars.min.js',
    './js/libs/bootstrap.min.js',
    './js/libs/jquery.fancybox.js',
    './js/libs/jquery-ui.min.js',
    './js/toastr.min.js',

    //CSS
    'https://fonts.googleapis.com/icon?family=Material+Icons',
    'https://fonts.googleapis.com/css?family=Poppins:300,400,700',
    './css/libs/bootstrap.min.css',
    './css/libs/owl.carousel.css',
];

function limpiarCache(cacheName, numeroItems) {

    caches.open(cacheName)
        .then(cache => {
            cache.keys()
                .then(keys => {
                    if (keys.length > numeroItems) {
                        cache.delete(keys[0])
                            .then(limpiarCache(cacheName, numeroItems));
                    }
                });
        });
}

self.addEventListener('install', e => {
    self.skipWaiting();
    const cacheStatic = caches.open(CACHE_STATIC_NAME)
        .then(cache => {
            cache.addAll(staticAssets);
        });

    const cacheInmutable = caches.open(CACHE_INMUTABLE_NAME)
        .then(cache => {
            cache.addAll(InmutableAssets);
        });

    e.waitUntil(Promise.all([cacheStatic, cacheInmutable]));
});

self.addEventListener('activate', e => {

    const respuesta = caches.keys().then(keys => {
        console.log("CACHES DISPONIBLES");
        keys.forEach(key => {
            console.log(key);
            if (key !== CACHE_STATIC_NAME && key !== CACHE_DYNAMIC_NAME && key !== CACHE_INMUTABLE_NAME) {
                caches.delete(key);
            }
        });
        return self.clients.claim();
    });

    e.waitUntil(respuesta);
    saveVersionInStorage();
});

self.addEventListener('fetch', e => {
    //ESTA ME CONVIENE POR EL MOMENTO
    //2-Primero Cache luego Internet y guarda todo lo que pregunta

    let respuesta;
    //VALIDAR API
    if (e.request.url.includes('api.mie-commerce')) {
        respuesta = manejoApiMensajes(CACHE_DYNAMIC_NAME, e.request);
    } else if (e.request.url.includes('paymentez')) {
        return fetch(e.request);
    } else if (e.request.url.includes('facebook') || e.request.url.includes('google')) {
        return fetch(e.request);
    } else {
        respuesta = caches.match(e.request)
            .then(res => {
                if (res) return res;

                return fetch(e.request).then(newResp => {
                    caches.open(CACHE_DYNAMIC_NAME)
                        .then(cache => {
                            cache.put(e.request, newResp);
                            limpiarCache(CACHE_DYNAMIC_NAME, CACHE_DYNAMIC_LIMIT);
                        });

                    return newResp.clone();
                })
                    .catch(err => {

                        if (e.request.headers.get('accept').includes('text/html')) {
                            return caches.match('./nointernet.html');
                        }
                    });

            });
    }

    e.respondWith(respuesta);
});

self.addEventListener('notificationclick', e => {
    console.log(e);
    /*
    const notification = e.notification;
    const accion = 
    e.respondWith( respuesta ); */
});

function manejoApiMensajes(cacheName, req) {
    if (req.clone().method == 'POST') {
        return fetch(req);
    } else {
        return fetch(req).then(res => {
            if (res.ok) {
                caches.open(cacheName)
                    .then(cache => {
                        cache.put(req, res.clone());
                    });
                return res.clone();
            } else {
                return caches.match(req);
            }
        }).catch(err => {
            return caches.match(req);
        });
    }
}

function saveVersionInStorage() {
    if (Notification.permission === "granted") {
        return self.registration.showNotification("Tienes una nueva versión de la web",
            {
                body: 'Por favor vuelve a actualizar la página para ver los cambios',
                icon: './images/logo.png',
                data: {
                    accion: "reload"
                },
            });
    }
}

// Your web apps Firebase configuration
var firebaseConfig = {
    apiKey: "AIzaSyBuP_Fx1kv9BLaj14anutR9KBQqnG48FdY",
    authDomain: "danilo-restaurante.firebaseapp.com",
    databaseURL: "https://danilo-restaurante.firebaseio.com",
    projectId: "danilo-restaurante",
    storageBucket: "danilo-restaurante.appspot.com",
    messagingSenderId: "535348482663",
    appId: "1:535348482663:web:0a7d39a032d8ea3f12fac2",
    measurementId: "G-WBNDG2QM4T"
};
// Initialize Firebase
firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();


messaging.setBackgroundMessageHandler(function (payload) {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    // Customize notification here

    const notificationTitle = payload.data.title;
    const notificationOptions = {
        body: payload.data.message,
        icon: './images/logo.png'
    };

    return self.registration.showNotification(notificationTitle,
        notificationOptions);
});