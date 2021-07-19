/*
Give the service worker access to Firebase Messaging.
Note that you can only use Firebase Messaging here, other Firebase libraries are not available in the service worker.
*/
importScripts("https://www.gstatic.com/firebasejs/8.3.1/firebase-app.js");
importScripts("https://www.gstatic.com/firebasejs/8.3.1/firebase-analytics.js");
importScripts("https://www.gstatic.com/firebasejs/8.3.1/firebase-messaging.js");

/*
Initialize the Firebase app in the service worker by passing in the messagingSenderId.
* New configuration for app@pulseservice.com
*/
firebase.initializeApp({
apiKey: 'AIzaSyAYpDhfkQfBKZLuENryyzw1MXQuRftLEp8',
projectId: 'arepita-2ac71',
messagingSenderId: '228959315555',
appId: '1:228959315555:web:f2792619b993c8d98d3a56',
});
/*
Retrieve an instance of Firebase Messaging so that it can handle background messages.
*/
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function(payload) {
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: payload.notification.icon,
    };

    return self.registration.showNotification(
        notificationTitle,
        notificationOptions
    );
});