/**
 * Firebase Cloud Messaging Service
 * This file provides utilities for handling push notifications
 */

import { messaging } from './config.js';
import { getToken, onMessage } from 'firebase/messaging';

class FirebaseMessaging {
    constructor() {
        this.messaging = messaging;
        this.currentToken = null;
    }

    /**
     * Request notification permission from the user
     * @returns {Promise<boolean>} True if permission granted, false otherwise
     */
    async requestPermission() {
        if (!this.messaging) {
            console.warn('Firebase Messaging is not available');
            return false;
        }

        try {
            const permission = await Notification.requestPermission();
            if (permission === 'granted') {
                console.log('Notification permission granted.');
                return true;
            } else {
                console.log('Notification permission denied.');
                return false;
            }
        } catch (error) {
            console.error('Error requesting notification permission:', error);
            return false;
        }
    }

    /**
     * Get FCM registration token
     * @param {string} vapidKey - VAPID key for web push (optional, can be set in service worker)
     * @returns {Promise<string|null>} FCM token or null if unavailable
     */
    async getToken(vapidKey = null) {
        console.log('[FCM] getToken called', { hasVapidKey: !!vapidKey });

        if (!this.messaging) {
            console.warn('[FCM] Firebase Messaging is not available');
            return null;
        }

        try {
            // Check if permission is granted
            console.log('[FCM] Checking notification permission:', Notification.permission);
            if (Notification.permission !== 'granted') {
                console.log('[FCM] Permission not granted, requesting permission...');
                const granted = await this.requestPermission();
                if (!granted) {
                    console.warn('[FCM] Permission request denied or failed');
                    return null;
                }
                console.log('[FCM] Permission granted successfully');
            }

            // Get service worker registration
            console.log('[FCM] Getting service worker registration...');
            const serviceWorkerRegistration = await navigator.serviceWorker.ready;
            console.log('[FCM] Service worker ready:', !!serviceWorkerRegistration);

            // Get token
            console.log('[FCM] Requesting FCM token from Firebase...');
            const token = await getToken(this.messaging, {
                vapidKey: vapidKey || undefined,
                serviceWorkerRegistration: serviceWorkerRegistration
            });

            if (token) {
                this.currentToken = token;
                console.log('[FCM] ✅ FCM Registration token obtained:', token.substring(0, 50) + '...');
                return token;
            } else {
                console.warn('[FCM] ⚠️ No registration token available from Firebase');
                return null;
            }
        } catch (error) {
            console.error('[FCM] ❌ Error occurred while retrieving token:', error);
            console.error('[FCM] Error details:', {
                name: error.name,
                message: error.message,
                stack: error.stack
            });
            return null;
        }
    }

    /**
     * Listen for foreground messages (when app is open)
     * @param {Function} callback - Callback function to handle messages
     */
    onMessage(callback) {
        if (!this.messaging) {
            console.warn('Firebase Messaging is not available');
            return;
        }

        onMessage(this.messaging, (payload) => {
            console.log('Message received in foreground:', payload);
            if (callback && typeof callback === 'function') {
                callback(payload);
            }
        });
    }

    /**
     * Send token to server (you can customize this method)
     * @param {string} token - FCM token
     * @param {string} endpoint - API endpoint to send token to
     */
    async sendTokenToServer(token, endpoint = '/api/fcm/token') {
        console.log('[FCM] sendTokenToServer called', {
            endpoint,
            tokenLength: token?.length || 0,
            tokenPreview: token ? token.substring(0, 50) + '...' : 'null'
        });

        if (!token) {
            console.error('[FCM] ❌ Cannot send token: token is null or empty');
            return false;
        }

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            };

            // Add CSRF token if available (for web routes) or use Sanctum token
            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken;
                console.log('[FCM] CSRF token found and added to headers');
            } else {
                console.warn('[FCM] ⚠️ CSRF token not found in meta tag');
            }

            console.log('[FCM] Sending POST request to:', endpoint);
            console.log('[FCM] Request headers:', { ...headers, 'X-CSRF-TOKEN': csrfToken ? '***' : 'missing' });
            console.log('[FCM] Request body:', { token: token.substring(0, 50) + '...' });

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: headers,
                credentials: 'same-origin', // Important for Sanctum cookie-based auth
                body: JSON.stringify({ token })
            });

            console.log('[FCM] Response received:', {
                status: response.status,
                statusText: response.statusText,
                ok: response.ok,
                headers: Object.fromEntries([...response.headers.entries()])
            });

            if (response.ok) {
                const data = await response.json();
                console.log('[FCM] ✅ Token sent to server successfully:', data);
                return true;
            } else {
                let errorData;
                try {
                    errorData = await response.json();
                } catch (e) {
                    const textResponse = await response.text();
                    errorData = { message: textResponse || 'Unknown error' };
                }
                console.error('[FCM] ❌ Failed to send token to server:', {
                    status: response.status,
                    statusText: response.statusText,
                    error: errorData
                });
                return false;
            }
        } catch (error) {
            console.error('[FCM] ❌ Exception occurred while sending token to server:', error);
            console.error('[FCM] Error details:', {
                name: error.name,
                message: error.message,
                stack: error.stack
            });
            return false;
        }
    }

    /**
     * Initialize messaging service
     * @param {Object} options - Configuration options
     * @param {string} options.vapidKey - VAPID key for web push
     * @param {string} options.tokenEndpoint - API endpoint to send token to
     * @param {Function} options.onMessageCallback - Callback for foreground messages
     * @param {boolean} options.autoRequestPermission - Automatically request permission
     */
    async init(options = {}) {
        console.log('[FCM] Initializing Firebase Messaging...', {
            hasVapidKey: !!options.vapidKey,
            tokenEndpoint: options.tokenEndpoint,
            hasCallback: !!options.onMessageCallback,
            autoRequestPermission: options.autoRequestPermission
        });

        const {
            vapidKey = null,
            tokenEndpoint = '/api/fcm/token',
            onMessageCallback = null,
            autoRequestPermission = false
        } = options;

        if (!this.messaging) {
            console.warn('[FCM] ❌ Firebase Messaging is not available. Make sure service worker is registered.');
            return false;
        }

        try {
            // Set up foreground message listener
            if (onMessageCallback) {
                console.log('[FCM] Setting up foreground message listener...');
                this.onMessage(onMessageCallback);
            }

            // Request permission if needed
            if (autoRequestPermission) {
                console.log('[FCM] Auto-requesting notification permission...');
                const permissionGranted = await this.requestPermission();
                if (!permissionGranted) {
                    console.warn('[FCM] ⚠️ Permission not granted, token may not be available');
                }
            }

            // Get and send token
            console.log('[FCM] Getting FCM token...');
            const token = await this.getToken(vapidKey);

            if (token && tokenEndpoint) {
                console.log('[FCM] Token obtained, sending to server...');
                const sent = await this.sendTokenToServer(token, tokenEndpoint);
                if (sent) {
                    console.log('[FCM] ✅ Firebase Messaging initialized and token saved successfully');
                } else {
                    console.error('[FCM] ❌ Failed to send token to server');
                }
            } else {
                if (!token) {
                    console.warn('[FCM] ⚠️ No token obtained, cannot send to server');
                }
                if (!tokenEndpoint) {
                    console.warn('[FCM] ⚠️ No token endpoint provided');
                }
            }

            return true;
        } catch (error) {
            console.error('[FCM] ❌ Error during initialization:', error);
            console.error('[FCM] Error details:', {
                name: error.name,
                message: error.message,
                stack: error.stack
            });
            return false;
        }
    }
}

// Create singleton instance
const firebaseMessaging = new FirebaseMessaging();

export default firebaseMessaging;
