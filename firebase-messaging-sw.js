importScripts('https://www.gstatic.com/firebasejs/4.5.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/4.5.2/firebase-messaging.js');

if(typeof(messaging)!="undefined"){
    messaging.setBackgroundMessageHandler(function(payload) {
        // console.log('[firebase-messaging-sw.js] Received background message ', payload);
        // Customize notification here
        const notificationTitle = 'Background Message Title';
        const notificationOptions = {
            body: 'Background Message body.',
            icon: '/firebase-logo.png'
        };

        return self.registration.showNotification(notificationTitle,notificationOptions);
    });
}