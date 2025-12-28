/**
 * Firebase Cloud Messaging Service Worker
 * This service worker handles background push notifications
 */

// Import Firebase scripts
importScripts('https://www.gstatic.com/firebasejs/12.7.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/12.7.0/firebase-messaging-compat.js');

// Firebase configuration
const firebaseConfig = {
    apiKey: "AIzaSyB-h_XCrdqpO38AFP2ZvUdXd0Yq4MB104w",
    authDomain: "sels-e407c.firebaseapp.com",
    projectId: "sels-e407c",
    storageBucket: "sels-e407c.firebasestorage.app",
    messagingSenderId: "689946728760",
    appId: "1:689946728760:web:605322353c7706bb8409cd",
    measurementId: "G-SZF8EVP8Y1"
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);

// Retrieve an instance of Firebase Messaging
const messaging = firebase.messaging();

/**
 * Play notification sound in service worker using Web Audio API
 * Service workers have limited audio capabilities, so we use Web Audio API
 */
function playNotificationSoundSW() {
    try {
        // Create AudioContext in service worker
        const audioContext = new (self.AudioContext || self.webkitAudioContext)();

        // Create oscillator for first tone
        const oscillator1 = audioContext.createOscillator();
        const gainNode1 = audioContext.createGain();

        oscillator1.connect(gainNode1);
        gainNode1.connect(audioContext.destination);

        // First tone (higher pitch)
        oscillator1.frequency.setValueAtTime(800, audioContext.currentTime);
        oscillator1.type = 'sine';
        gainNode1.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode1.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);

        oscillator1.start(audioContext.currentTime);
        oscillator1.stop(audioContext.currentTime + 0.1);

        // Second tone (lower pitch) after delay
        setTimeout(() => {
            const oscillator2 = audioContext.createOscillator();
            const gainNode2 = audioContext.createGain();

            oscillator2.connect(gainNode2);
            gainNode2.connect(audioContext.destination);

            oscillator2.frequency.setValueAtTime(600, audioContext.currentTime);
            oscillator2.type = 'sine';
            gainNode2.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode2.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.15);

            oscillator2.start(audioContext.currentTime);
            oscillator2.stop(audioContext.currentTime + 0.15);
        }, 100);
    } catch (error) {
        console.log('[firebase-messaging-sw.js] Could not play sound in service worker:', error);
        // Browser's default notification sound will play if silent: false
    }
}

// Handle background messages
messaging.onBackgroundMessage((payload) => {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);

    const notificationTitle = payload.notification?.title || 'New Notification';
    const notificationOptions = {
        body: payload.notification?.body || '',
        icon: payload.notification?.icon || '/images/logo.png',
        badge: '/images/logo.png',
        image: payload.notification?.image,
        data: payload.data || {},
        tag: payload.data?.tag || 'default',
        requireInteraction: false,
        silent: false, // Set to false to enable sound
        sound: '/sounds/notification.mp3', // Path to sound file (browser may ignore this)
        vibrate: [200, 100, 200] // Vibration pattern for mobile devices
    };

    // Play sound using Web Audio API in service worker
    playNotificationSoundSW();

    return self.registration.showNotification(notificationTitle, notificationOptions);
});

// Handle notification click
self.addEventListener('notificationclick', (event) => {
    console.log('[firebase-messaging-sw.js] Notification click received.');

    event.notification.close();

    // Get the URL from notification data or use default
    const urlToOpen = event.notification.data?.url || '/';

    event.waitUntil(
        clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        }).then((clientList) => {
            // Check if there's already a window/tab open with the target URL
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url === urlToOpen && 'focus' in client) {
                    return client.focus();
                }
            }
            // If no window is open, open a new one
            if (clients.openWindow) {
                return clients.openWindow(urlToOpen);
            }
        })
    );
});
