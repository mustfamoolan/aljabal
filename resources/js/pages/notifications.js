/**
 * Notifications Page - Firebase Messaging Initialization
 * Initializes Firebase Cloud Messaging for push notifications
 */

import { initFirebaseMessaging } from '../firebase/init.js';

/**
 * Play notification sound
 * Uses Web Audio API to generate a pleasant notification sound
 */
function playNotificationSound() {
    try {
        // Try to play audio file first (if available)
        const audio = new Audio('/sounds/notification.mp3');
        audio.volume = 0.5; // Set volume to 50%

        audio.play().catch(error => {
            console.log('[Notifications] Audio file not found, using Web Audio API fallback:', error);
            // Fallback to Web Audio API if file doesn't exist
            playWebAudioNotification();
        });
    } catch (error) {
        console.log('[Notifications] Using Web Audio API fallback:', error);
        // Fallback to Web Audio API
        playWebAudioNotification();
    }
}

/**
 * Generate notification sound using Web Audio API
 */
function playWebAudioNotification() {
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();

        // Configure sound: pleasant two-tone notification
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);

        // First tone (higher pitch)
        oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
        oscillator.type = 'sine';
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);

        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.1);

        // Second tone (lower pitch) after a short delay
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
        console.warn('[Notifications] Could not play notification sound:', error);
    }
}

// Get VAPID key from meta tag or environment
function getVapidKey() {
    // Try to get from meta tag first
    const metaTag = document.querySelector('meta[name="fcm-vapid-key"]');
    if (metaTag) {
        return metaTag.getAttribute('content');
    }

    // Fallback to environment variable (if set in window)
    if (window.FCM_VAPID_KEY) {
        return window.FCM_VAPID_KEY;
    }

    return null;
}

// Initialize Firebase Messaging when page loads
document.addEventListener('DOMContentLoaded', async () => {
    // Only initialize if user is authenticated
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.log('User not authenticated, skipping Firebase initialization');
        return;
    }

    const vapidKey = getVapidKey();

    if (!vapidKey) {
        console.warn('FCM VAPID Key not found. Please add it to your .env file and pass it to the view.');
        return;
    }

    try {
        console.log('[Notifications] Initializing Firebase Messaging with options:', {
            hasVapidKey: !!vapidKey,
            tokenEndpoint: '/api/admin/fcm/token',
            autoRequestPermission: true,
        });

        const result = await initFirebaseMessaging({
            vapidKey: vapidKey,
            tokenEndpoint: '/api/admin/fcm/token',
            autoRequestPermission: true,
            onMessageCallback: (payload) => {
                console.log('[Notifications] Foreground notification received:', payload);

                // Play notification sound
                playNotificationSound();

                // Show browser notification if permission granted
                if (Notification.permission === 'granted') {
                    const notification = payload.notification || {};
                    const data = payload.data || {};

                    // Create browser notification
                    const browserNotification = new Notification(notification.title || 'إشعار جديد', {
                        body: notification.body || '',
                        icon: '/images/logo-sm.png',
                        badge: '/images/logo-sm.png',
                        tag: data.notification_id || 'notification',
                        data: data,
                    });

                    // Handle click on notification
                    browserNotification.onclick = function(event) {
                        event.preventDefault();
                        window.focus();

                        // Navigate to notification URL if available
                        if (data.url) {
                            window.location.href = data.url;
                        } else if (data.notification_id) {
                            window.location.href = `/notifications/${data.notification_id}`;
                        } else {
                            window.location.href = '/notifications';
                        }

                        this.close();
                    };

                    // Auto close after 5 seconds
                    setTimeout(() => {
                        browserNotification.close();
                    }, 5000);
                }

                // Update badge count in topbar if available
                if (window.topbarNotifications && typeof window.topbarNotifications.updateBadgeCount === 'function') {
                    window.topbarNotifications.updateBadgeCount();
                }

                // Reload notifications list if on notifications page
                if (window.location.pathname.includes('/notifications')) {
                    // Trigger a custom event to reload notifications
                    window.dispatchEvent(new CustomEvent('notification-received', { detail: payload }));
                }
            }
        });

        if (result) {
            console.log('[Notifications] ✅ Firebase Messaging initialized successfully');
        } else {
            console.error('[Notifications] ❌ Firebase Messaging initialization returned false');
        }
    } catch (error) {
        console.error('[Notifications] ❌ Error initializing Firebase Messaging:', error);
        console.error('[Notifications] Error details:', {
            name: error.name,
            message: error.message,
            stack: error.stack
        });
    }
});

// Export for use in other modules
export default {};
