/**
 * Firebase Initialization Example
 * This file shows how to initialize Firebase Messaging
 * You can import this in your app.js or any page where you need notifications
 */

import firebaseMessaging from './messaging.js';

/**
 * Initialize Firebase Messaging
 * Call this function when you want to set up push notifications
 *
 * @param {Object} options - Configuration options
 */
export async function initFirebaseMessaging(options = {}) {
    const defaultOptions = {
        // VAPID Key - Get this from Firebase Console > Project Settings > Cloud Messaging
        // vapidKey: 'YOUR_VAPID_KEY_HERE',

        // API endpoint to send FCM token to your backend
        tokenEndpoint: '/api/fcm/token',

        // Callback function for foreground messages
        onMessageCallback: (payload) => {
            console.log('Foreground message received:', payload);

            // You can show a custom notification here
            // Example: showCustomNotification(payload);
        },

        // Automatically request notification permission
        autoRequestPermission: true
    };

    const config = { ...defaultOptions, ...options };

    try {
        // Register service worker first
        if ('serviceWorker' in navigator) {
            const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
            console.log('Service Worker registered:', registration);
        }

        // Initialize messaging
        const initialized = await firebaseMessaging.init(config);

        if (initialized) {
            console.log('Firebase Messaging initialized successfully');
            return firebaseMessaging;
        } else {
            console.warn('Firebase Messaging initialization failed');
            return null;
        }
    } catch (error) {
        console.error('Error initializing Firebase Messaging:', error);
        return null;
    }
}

/**
 * Example: Show custom notification
 * You can customize this function based on your needs
 */
export function showCustomNotification(payload) {
    const notification = payload.notification;

    if (Notification.permission === 'granted') {
        new Notification(notification.title || 'New Notification', {
            body: notification.body || '',
            icon: notification.icon || '/images/logo.png',
            badge: '/images/logo.png',
            image: notification.image,
            data: payload.data || {},
            tag: payload.data?.tag || 'default'
        });
    }
}

// Export the messaging instance for direct use
export { default as firebaseMessaging } from './messaging.js';
